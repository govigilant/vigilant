<?php

namespace Vigilant\Crawler\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Crawler\Actions\CrawUrl;
use Vigilant\Crawler\Models\CrawledUrl;

class CrawUrlJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public CrawledUrl $url
    ) {
        $this->onQueue(config('crawler.queue'));
    }

    public function handle(CrawUrl $url): void
    {
        $url->crawl($this->url);
    }

    public function uniqueId(): string
    {
        return $this->url->uuid;
    }
}
