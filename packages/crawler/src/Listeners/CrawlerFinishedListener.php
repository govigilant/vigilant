<?php

namespace Vigilant\Crawler\Listeners;

use Vigilant\Crawler\Events\CrawlerFinishedEvent;
use Vigilant\Crawler\Jobs\CollectCrawlerStatsJob;

class CrawlerFinishedListener
{
    public function handle(CrawlerFinishedEvent $event): void
    {
        CollectCrawlerStatsJob::dispatch($event->crawler);
    }
}
