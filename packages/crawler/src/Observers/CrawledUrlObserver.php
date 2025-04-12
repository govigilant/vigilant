<?php

namespace Vigilant\Crawler\Observers;

use Vigilant\Crawler\Models\CrawledUrl;

class CrawledUrlObserver
{
    public function creating(CrawledUrl $url): void
    {
        if ($url->url_hash === null) {
            $url->url_hash = md5($url->url);
        }
    }
}
