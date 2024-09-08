<?php

namespace Vigilant\Crawler\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Crawler\Actions\ImportSitemaps;
use Vigilant\Crawler\Models\Crawler;

class ImportSitemapsJob implements ShouldBeUnique, ShouldQueue
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

    public function handle(ImportSitemaps $sitemaps): void
    {
        $sitemaps->import($this->crawler);
    }

    public function uniqueId(): string
    {
        return $this->crawler->id;
    }
}
