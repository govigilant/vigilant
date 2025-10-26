<?php

namespace Vigilant\Crawler\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Crawler\Actions\CollectCrawlerStats;
use Vigilant\Crawler\Models\Crawler;

class CollectCrawlerStatsJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Crawler $crawler
    ) {
        $this->onQueue(config('crawler.queue'));
    }

    public function handle(CollectCrawlerStats $crawlerStats): void
    {
        $crawlerStats->collect($this->crawler);
    }

    public function uniqueId(): int
    {
        return $this->crawler->id;
    }
}
