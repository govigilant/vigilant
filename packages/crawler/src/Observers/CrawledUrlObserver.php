<?php

namespace Vigilant\Crawler\Observers;

use Vigilant\Crawler\Models\CrawledUrl;
use Vigilant\Crawler\Models\IgnoredUrl;

class CrawledUrlObserver
{
    public function creating(CrawledUrl $url): void
    {
        $url->hash();

        $url->ignored = IgnoredUrl::query()
            ->where('crawler_id', '=', $url->crawler_id)
            ->where('url_hash', '=', $url->url_hash)
            ->exists();
    }
}
