<?php

namespace Vigilant\Crawler\Actions;

use Illuminate\Support\Facades\Http;
use Mtownsend\XmlToArray\XmlToArray;
use Vigilant\Crawler\Models\CrawledUrl;
use Vigilant\Crawler\Models\Crawler;

class ImportSitemaps
{
    public function import(Crawler $crawler): void
    {
        foreach ($crawler->sitemaps as $sitemap) {

            $response = Http::get($sitemap);

            if (! $response->successful()) {
                continue;
            }

            $xml = $response->body();

            $sitemap = XmlToArray::convert($xml);

            $urls = collect($sitemap['url'] ?? [])->pluck('loc');

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
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])->toArray()
                );
            }
        }
    }
}
