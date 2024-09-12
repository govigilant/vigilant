<?php

namespace Vigilant\Crawler\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Vigilant\Crawler\Models\Crawler;

class CrawlerFinishedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Crawler $crawler) {}
}
