<?php

namespace Vigilant\Crawler\Commands;

use Illuminate\Console\Command;
use Vigilant\Core\Services\TeamService;
use Vigilant\Crawler\Actions\StartCrawler;
use Vigilant\Crawler\Models\Crawler;

class StartCrawlerCommand extends Command
{
    protected $signature = 'crawler:start {crawlerId}';

    protected $description = 'Start a crawler';

    public function handle(StartCrawler $starter, TeamService $teamService): int
    {
        /** @var int $crawlerId */
        $crawlerId = $this->argument('crawlerId');

        /** @var Crawler $crawler */
        $crawler = Crawler::query()->withoutGlobalScopes()->findOrFail($crawlerId);

        $teamService->setTeamById($crawler->team_id);

        $starter->start($crawler);

        return static::SUCCESS;
    }
}
