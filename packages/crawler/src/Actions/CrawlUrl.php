<?php

namespace Vigilant\Crawler\Actions;

use DOMDocument;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Vigilant\Core\Services\TeamService;
use Vigilant\Crawler\Enums\State;
use Vigilant\Crawler\Models\CrawledUrl;
use Vigilant\Crawler\Notifications\RatelimitedNotification;

class CrawlUrl
{
    public function __construct(protected TeamService $teamService) {}

    public function crawl(CrawledUrl $url): void
    {
        $this->teamService->setTeamById($url->team_id);

        if (! Gate::check('create-crawled-url', $url->crawler)) {
            $url->crawler->update([
                'state' => State::Limited,
            ]);

            return;
        }

        try {
            $response = Http::timeout(config()->integer('crawler.timeout'))
                ->connectTimeout(config()->integer('crawler.timeout'))
                ->withOptions(['verify' => false, 'allow_redirects' => false])
                ->withUserAgent(config('core.user_agent'))
                ->get($url->url);
        } catch (ConnectionException) {
            $url->update([
                'status' => 0,
                'crawled' => true,
            ]);

            return;
        }

        /** @var array $baseUrl */
        $baseUrl = parse_url($url->url);

        if (! $response->successful()) {
            $url->update([
                'status' => $response->status(),
                'crawled' => true,
            ]);

            if ($response->status() === 429 && $url->crawler !== null) {
                $url->crawler->update([
                    'state' => State::Ratelimited,
                ]);

                RatelimitedNotification::notify($url->crawler);
            }

            if ($response->redirect()) {

                $redirectUrl = $response->header('Location');

                if (! $this->isSameDomain($redirectUrl, $baseUrl['host'])) {
                    return;
                }

                if (! filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
                    $redirectUrl = $this->resolveRelativeUrl($redirectUrl, parse_url($url->url)); // @phpstan-ignore-line
                }

                if (! Gate::check('create-crawled-url', $url->crawler)) {
                    $redirectUrl = str($redirectUrl)->limit(8192)->toString();

                    CrawledUrl::query()->firstOrCreate([
                        'crawler_id' => $url->crawler_id,
                        'url_hash' => md5($redirectUrl),
                    ], [
                        'url' => $redirectUrl,
                        'found_on_id' => $url->uuid,
                    ]);
                }
            }

            return;
        }

        $html = $response->body();

        $dom = new DOMDocument;
        @$dom->loadHTML($html); // Suppress warnings due to potential malformed HTML

        $links = [];

        if (! array_key_exists('host', $baseUrl)) {
            $url->update(['crawled' => true]);

            return;
        }

        foreach ($dom->getElementsByTagName('a') as $anchor) {

            $href = $anchor->getAttribute('href');

            if (! $href) {
                continue;
            }

            if (! filter_var($href, FILTER_VALIDATE_URL)) {
                $href = $this->resolveRelativeUrl($href, $baseUrl);
            }

            if (! $this->isSameDomain($href, $baseUrl['host']) || ! filter_var($href, FILTER_VALIDATE_URL)) {
                continue;
            }

            $href = rtrim($href, '/#');

            $links[] = $href;
        }

        foreach ($links as $link) {
            if (! Gate::check('create-crawled-url', $url->crawler)) { // @phpstan-ignore-line
                break;
            }

            $pageUrl = str($link)->limit(8192)->toString();

            CrawledUrl::query()->firstOrCreate([
                'crawler_id' => $url->crawler_id,
                'url_hash' => md5($pageUrl),
            ], [
                'url' => str($link)->limit(8192)->toString(),
                'found_on_id' => $url->uuid,
            ]);
        }

        $url->update([
            'status' => $response->status(),
            'crawled' => true,
        ]);
    }

    protected function isSameDomain(string $url, string $domain): bool
    {
        $parsedUrl = parse_url($url, PHP_URL_HOST);

        return $parsedUrl && str_starts_with($parsedUrl, $domain);
    }

    protected function resolveRelativeUrl(string $relativeUrl, array $baseUrlParts): string
    {
        // If the relative URL starts with "//", it refers to a protocol-relative URL
        if (strpos($relativeUrl, '//') === 0) {
            return $baseUrlParts['scheme'].':'.$relativeUrl;
        }

        // If the relative URL starts with "/", it's an absolute path relative to the domain
        if (strpos($relativeUrl, '/') === 0) {
            return $baseUrlParts['scheme'].'://'.$baseUrlParts['host'].$relativeUrl;
        }

        // Otherwise, it's a relative path, resolve by appending to base path
        $basePath = isset($baseUrlParts['path']) ? dirname($baseUrlParts['path']) : '';

        return $baseUrlParts['scheme'].'://'.$baseUrlParts['host'].$basePath.'/'.ltrim($relativeUrl, '/');
    }
}
