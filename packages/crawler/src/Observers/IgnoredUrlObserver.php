<?php

namespace Vigilant\Crawler\Observers;

use Vigilant\Crawler\Jobs\CollectCrawlerStatsJob;
use Vigilant\Crawler\Models\IgnoredUrl;

class IgnoredUrlObserver
{
    public function created(IgnoredUrl $url): void
    {
        CollectCrawlerStatsJob::dispatch($url->crawler, false);
    }
}
