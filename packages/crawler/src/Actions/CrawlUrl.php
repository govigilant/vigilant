<?php

namespace Vigilant\Crawler\Actions;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Vigilant\Core\Services\TeamService;
use Vigilant\Crawler\Enums\State;
use Vigilant\Crawler\Models\CrawledUrl;
use Vigilant\Crawler\Notifications\RatelimitedNotification;

class CrawlUrl
{
    protected const MAX_REDIRECTS = 5;

    public function __construct(protected TeamService $teamService) {}

    public function crawl(CrawledUrl $url, int $try = 0): void
    {
        $this->teamService->setTeamById($url->team_id);

        if (! Gate::check('create-crawled-url', $url->crawler)) {
            $url->crawler->update([
                'state' => State::Limited,
            ]);

            return;
        }

        $allowedHost = parse_url($url->url, PHP_URL_HOST);

        if (! is_string($allowedHost)) {
            $url->update([
                'status' => 0,
                'crawled' => true,
            ]);

            return;
        }

        try {
            $response = $this->fetchResponse($url->url, $allowedHost);
        } catch (ConnectionException) {
            if ($try < 3) {
                $this->crawl($url, $try + 1);

                return;
            }

            $url->update([
                'status' => 0,
                'crawled' => true,
            ]);

            return;
        }

        $baseUrl = parse_url($url->url) ?: [];

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

            return;
        }

        $html = $response->body();

        if (! isset($baseUrl['host'], $baseUrl['scheme'])) {
            $url->update(['crawled' => true]);

            return;
        }

        $links = $this->extractLinks($html, $baseUrl);
        $queuedLinks = [];

        foreach ($links as $link) {
            if (! Gate::check('create-crawled-url', $url->crawler)) { // @phpstan-ignore-line
                break;
            }

            if (strlen($link) > 8192) {
                $link = substr($link, 0, 8192);
            }

            $hash = md5($link);

            $queuedLinks[$hash] = [
                'crawler_id' => $url->crawler_id,
                'url_hash' => $hash,
                'url' => $link,
                'found_on_id' => $url->uuid,
            ];
        }

        if ($queuedLinks !== []) {
            $timestamp = now();
            $records = [];

            foreach ($queuedLinks as $record) {
                $records[] = [
                    'uuid' => (string) Str::uuid(),
                    'crawler_id' => $record['crawler_id'],
                    'team_id' => $url->team_id,
                    'url_hash' => $record['url_hash'],
                    'url' => $record['url'],
                    'found_on_id' => $record['found_on_id'],
                    'crawled' => false,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            CrawledUrl::query()->insertOrIgnore($records);
        }

        $url->update([
            'status' => $response->status(),
            'crawled' => true,
        ]);
    }

    protected function extractLinks(string $html, array $baseUrl): array
    {
        if ($html === '' || stripos($html, '<a') === false || ! isset($baseUrl['host'], $baseUrl['scheme'])) {
            return [];
        }

        $pattern = '~<a\b[^>]*\bhref\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>"\']+))~i';

        if (! preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) {
            return [];
        }

        $links = [];

        foreach ($matches as $match) {
            $href = $match[1] ?? $match[2] ?? $match[3] ?? '';
            $href = html_entity_decode(trim($href), ENT_QUOTES | ENT_HTML5);

            if ($href === '' || $href === '#') {
                continue;
            }

            $lowerHref = strtolower($href);

            if (str_starts_with($lowerHref, 'mailto:') || str_starts_with($lowerHref, 'tel:') || str_starts_with($lowerHref, 'javascript:')) {
                continue;
            }

            if (! filter_var($href, FILTER_VALIDATE_URL)) {
                $href = $this->resolveRelativeUrl($href, $baseUrl);
            }

            if (! filter_var($href, FILTER_VALIDATE_URL) || ! $this->isSameDomain($href, $baseUrl['host'])) {
                continue;
            }

            $href = $this->withoutQuery($href);

            $normalized = rtrim($href, '/#');

            if ($normalized === '') {
                continue;
            }

            $links[$normalized] = true;
        }

        return array_keys($links);
    }

    protected function fetchResponse(string $currentUrl, ?string $allowedDomain, int $redirectCount = 0): Response
    {
        $response = $this->sendRequest($currentUrl);

        $nextUrl = $this->nextRedirectUrl($response, $currentUrl, $allowedDomain, $redirectCount);

        if ($nextUrl !== null) {
            return $this->fetchResponse($nextUrl, $allowedDomain, $redirectCount + 1);
        }

        return $response;
    }

    protected function sendRequest(string $url): Response
    {
        $timeout = config()->integer('crawler.timeout');

        return Http::timeout($timeout)
            ->connectTimeout($timeout)
            ->withOptions(['verify' => false, 'allow_redirects' => false])
            ->withUserAgent(config('core.user_agent'))
            ->get($url);
    }

    protected function nextRedirectUrl(Response $response, string $currentUrl, ?string $allowedDomain, int $redirectCount): ?string
    {
        if (! $response->redirect() || $allowedDomain === null || $redirectCount >= self::MAX_REDIRECTS) {
            return null;
        }

        $redirectLocation = $response->header('Location');

        if (is_array($redirectLocation)) {
            $redirectLocation = $redirectLocation[0] ?? null;
        }

        if (! is_string($redirectLocation) || $redirectLocation === '') {
            return null;
        }

        if (! filter_var($redirectLocation, FILTER_VALIDATE_URL)) {
            $baseParts = parse_url($currentUrl);

            if ($baseParts === false || ! isset($baseParts['scheme'], $baseParts['host'])) {
                return null;
            }

            $redirectLocation = $this->resolveRelativeUrl($redirectLocation, $baseParts);
        }

        return $this->isSameDomain($redirectLocation, $allowedDomain)
            ? $redirectLocation
            : null;
    }

    protected function isSameDomain(string $url, string $domain): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        if ($host === null || $host === false) {
            return false;
        }

        $host = strtolower($host);
        $domain = strtolower($domain);

        // Match exact domain or proper subdomain (with dot boundary)
        return $host === $domain || preg_match('/\.'.preg_quote($domain, '/').'$/', $host);
    }

    protected function withoutQuery(string $url): string
    {
        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            return $url;
        }

        $scheme = $parts['scheme'];
        $host = $parts['host'];
        $userInfo = '';

        if (isset($parts['user'])) {
            $userInfo = $parts['user'];

            if (isset($parts['pass'])) {
                $userInfo .= ':'.$parts['pass'];
            }

            $userInfo .= '@';
        }

        $port = isset($parts['port']) ? ':'.$parts['port'] : '';
        $path = $parts['path'] ?? '';

        if ($path === '') {
            $path = '/';
        }

        return $scheme.'://'.$userInfo.$host.$port.$path;
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

        if ($basePath === '/') {
            $basePath = '';
        }

        return $baseUrlParts['scheme'].'://'.$baseUrlParts['host'].$basePath.'/'.ltrim($relativeUrl, '/');
    }
}
