<?php

namespace Vigilant\Crawler\Commands;

use Illuminate\Console\Command;
use Vigilant\Core\Services\TeamService;
use Vigilant\Crawler\Jobs\CollectCrawlerStatsJob;
use Vigilant\Crawler\Models\Crawler;

class CollectCrawlerStatsCommand extends Command
{
    protected $signature = 'crawler:stats {crawlerId}';

    protected $description = 'Collect ';

    public function handle(TeamService $teamService): int
    {
        /** @var int $crawlerId */
        $crawlerId = $this->argument('crawlerId');

        /** @var Crawler $crawler */
        $crawler = Crawler::query()->withoutGlobalScopes()->findOrFail($crawlerId);

        CollectCrawlerStatsJob::dispatch($crawler);

        return static::SUCCESS;
    }
}
