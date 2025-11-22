<?php

namespace Vigilant\Crawler\Observers;

use Vigilant\Crawler\Actions\StartCrawler;
use Vigilant\Crawler\Models\Crawler;

class CrawlerObserver
{
    public function __construct(
        protected StartCrawler $startCrawler,
    ) {
    }

    public function created(Crawler $crawler): void
    {
        $this->startCrawler->start($crawler);
    }
}
