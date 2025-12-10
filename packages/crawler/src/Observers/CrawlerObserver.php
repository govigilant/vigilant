<?php

namespace Vigilant\Crawler\Observers;

use Vigilant\Crawler\Jobs\StartCrawlerJob;
use Vigilant\Crawler\Models\Crawler;

class CrawlerObserver
{
    public function created(Crawler $crawler): void
    {
        StartCrawlerJob::dispatch($crawler);
    }
}
