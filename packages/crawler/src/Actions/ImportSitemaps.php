<?php

namespace Vigilant\Crawler\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Mtownsend\XmlToArray\XmlToArray;
use Vigilant\Crawler\Models\CrawledUrl;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Crawler\Models\IgnoredUrl;

class ImportSitemaps
{
    protected array $processed = [];

    public function import(Crawler $crawler): void
    {
        foreach ($crawler->sitemaps ?? [] as $sitemap) {
            $this->processSitemap($crawler, $sitemap);
        }
    }

    protected function processSitemap(Crawler $crawler, string $url): void
    {
        if (in_array($url, $this->processed, true)) {
            return;
        }

        $this->processed[] = $url;

        $response = Http::withHeaders([
            'Accept' => 'application/xml',
        ])->get($url);

        if (! $response->successful()) {
            return;
        }

        $xml = $response->body();
        $parsed = XmlToArray::convert($xml, true);

        $root = $parsed['@root'] ?? 'urlset';

        if ($root === 'urlset') {
            /** @var array<int, string> $urls */
            $urls = $parsed['url'] ?? [];
            $urls = collect($urls)->pluck('loc')->filter();

            $this->storeUrls($crawler, $urls);
        }

        if ($root === 'sitemapindex') {
            /** @var array<int, string> $sitemaps */
            $sitemaps = $parsed['sitemap'] ?? [];

            $nested = collect($sitemaps)->pluck('loc')->filter();

            foreach ($nested as $nestedSitemapUrl) {
                $this->processSitemap($crawler, $nestedSitemapUrl);
            }
        }
    }

    protected function storeUrls(Crawler $crawler, Collection $urls): void
    {
        $chunks = $urls->chunk(5000);

        foreach ($chunks as $chunk) {
            $existingUrls = $crawler->urls()->whereIn('url', $chunk)->pluck('url');

            $newUrls = $chunk->diff($existingUrls);

            if ($newUrls->isEmpty()) {
                continue;
            }

            $timestamp = now();
            $urlHashes = $newUrls->map(fn ($url): string => md5($url));
            $ignoredHashes = $urlHashes->isEmpty()
                ? []
                : IgnoredUrl::query()
                    ->where('crawler_id', '=', $crawler->id)
                    ->whereIn('url_hash', $urlHashes->all())
                    ->pluck('url_hash')
                    ->all();

            $crawler->urls()->insert(
                $newUrls->map(function ($url) use ($crawler, $timestamp, $ignoredHashes): array {
                    $hash = md5($url);

                    return [
                        'uuid' => (new CrawledUrl)->newUniqueId(),
                        'crawler_id' => $crawler->id,
                        'team_id' => $crawler->team_id,
                        'url' => $url,
                        'url_hash' => $hash,
                        'ignored' => in_array($hash, $ignoredHashes, true),
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                })->toArray()
            );
        }
    }
}
