<?php

namespace Vigilant\Crawler\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\PendingDispatch;
use Vigilant\Crawler\Enums\State;
use Vigilant\Crawler\Jobs\ProcessCrawlerStateJob;
use Vigilant\Crawler\Models\Crawler;

class ProcessCrawlerStatesCommand extends Command
{
    protected $signature = 'crawler:process';

    protected $description = 'Process all crawlers';

    public function handle(): int
    {
        Crawler::query()
            ->withoutGlobalScopes()
            ->whereIn('state', [
                State::Pending,
                State::Crawling,
            ])
            ->get()
            ->each(fn (Crawler $crawler): PendingDispatch => ProcessCrawlerStateJob::dispatch($crawler));

        return static::SUCCESS;
    }
}
