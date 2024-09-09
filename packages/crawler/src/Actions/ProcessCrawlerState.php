<?php

namespace Vigilant\Crawler\Actions;

use Vigilant\Core\Services\TeamService;
use Vigilant\Crawler\Enums\State;
use Vigilant\Crawler\Events\CrawlerFinishedEvent;
use Vigilant\Crawler\Models\Crawler;

class ProcessCrawlerState
{
    public function __construct(
        protected TeamService $teamService,
        protected StartCrawler $starter,
    ) {}

    public function process(Crawler $crawler): void
    {
        $this->teamService->setTeamById($crawler->team_id);

        if ($crawler->state === State::Pending) {
            $this->starter->start($crawler);

            return;
        }

        if ($crawler->state === State::Crawling && $this->finished($crawler)) {
            $crawler->update([
                'state' => State::Finished,
            ]);

            event(new CrawlerFinishedEvent($crawler));

            return;
        }
    }

    protected function finished(Crawler $crawler): bool
    {
        $crawledUrlCount = $crawler
            ->urls()
            ->where('crawled', '=', true)
            ->count();

        $uncrawledUrlCount = $crawler
            ->urls()
            ->where('crawled', '=', false)
            ->count();

        return $crawledUrlCount > 0 && $uncrawledUrlCount === 0;
    }
}
