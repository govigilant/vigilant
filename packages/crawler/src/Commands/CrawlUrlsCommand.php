<?php

namespace Vigilant\Crawler\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\PendingDispatch;
use Vigilant\Crawler\Enums\State;
use Vigilant\Crawler\Jobs\CrawUrlJob;
use Vigilant\Crawler\Models\CrawledUrl;

class CrawlUrlsCommand extends Command
{
    protected $signature = 'crawler:crawl';

    protected $description = 'Crawl pending URLs';

    public function handle(): int
    {
        $count = config()->integer('crawler.crawls_per_minute');

        CrawledUrl::query()
            ->withoutGlobalScopes()
            ->where('crawled', '=', false)
            ->whereHas('crawler', fn (Builder $query) => $query->withoutGlobalScopes()->where('state', '=', State::Crawling))
            ->take($count)
            ->get()
            ->each(fn (CrawledUrl $url): PendingDispatch => CrawUrlJob::dispatch($url));

        return static::SUCCESS;
    }
}
