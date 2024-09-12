<?php

namespace Vigilant\Crawler\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Crawler\Actions\ProcessCrawlerState;
use Vigilant\Crawler\Models\Crawler;

class ProcessCrawlerStateJob implements ShouldBeUnique, ShouldQueue
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

    public function handle(ProcessCrawlerState $state): void
    {
        $state->process($this->crawler);
    }

    public function uniqueId(): int
    {
        return $this->crawler->id;
    }
}