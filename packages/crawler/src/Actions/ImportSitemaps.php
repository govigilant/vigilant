<?php

namespace Vigilant\Crawler\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Mtownsend\XmlToArray\XmlToArray;
use Vigilant\Crawler\Models\CrawledUrl;
use Vigilant\Crawler\Models\Crawler;

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
            /** @var Collection<int, string> $urls */
            $urls = collect($parsed['url'])->pluck('loc')->filter();

            $this->storeUrls($crawler, $urls);
        }

        if ($root === 'sitemapindex') {
            /** @var Collection<int, string> $nested */
            $nested = collect($parsed['sitemap'])->pluck('loc')->filter();

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

            $crawler->urls()->insert(
                $newUrls->map(fn ($url): array => [
                    'uuid' => (new CrawledUrl)->newUniqueId(),
                    'crawler_id' => $crawler->id,
                    'team_id' => $crawler->team_id,
                    'url' => $url,
                    'url_hash' => md5($url),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->toArray()
            );
        }
    }
}
