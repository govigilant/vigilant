<?php

namespace Vigilant\Crawler\Actions;

use Vigilant\Crawler\Enums\State;
use Vigilant\Crawler\Jobs\ImportSitemapsJob;
use Vigilant\Crawler\Models\Crawler;

class StartCrawler
{
    public function start(Crawler $crawler): void
    {
        $crawler->urls()->update([
            'crawled' => false,
        ]);

        $crawler->urls()->firstOrCreate([
            'url' => $crawler->start_url,
        ]);

        if ($crawler->sitemaps !== null && count($crawler->sitemaps) > 0) {
            ImportSitemapsJob::dispatch($crawler);
        }

        $crawler->update([
            'state' => State::Crawling,
            'crawler_stats' => null,
        ]);
    }
}
