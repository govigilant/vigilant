<?php

namespace Vigilant\Crawler\Observers;

use Vigilant\Crawler\Models\CrawledUrl;
use Vigilant\Crawler\Models\IgnoredUrl;

class CrawledUrlObserver
{
    public function creating(CrawledUrl $url): void
    {
        if ($url->url_hash === null) {
            $url->url_hash = md5($url->url);
        }

        $url->ignored = IgnoredUrl::query()
            ->where('crawler_id', '=', $url->crawler_id)
            ->where('url_hash', '=', $url->url_hash)
            ->exists();
    }
}
