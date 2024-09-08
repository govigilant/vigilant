<?php

namespace Vigilant\Crawler\Actions;

use Illuminate\Support\Collection;
use Vigilant\Core\Services\TeamService;
use Vigilant\Crawler\Models\Crawler;

class CollectCrawlerStats
{
    public function __construct(protected TeamService $teamService)
    {
    }

    public function collect(Crawler $crawler): void
    {
        $this->teamService->setTeamById($crawler->team_id);

        /** @var Collection<int, int> $statuses */
        $statuses = $crawler
            ->urls()
            ->where('crawled', '=', true)
            ->selectRaw('status, COUNT(*) AS count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $stats = [
            'total_url_count' => $statuses->sum(),
            'statuses' => $statuses,
            'issue_count' => $crawler->urls()
                ->where('crawled', '=', true)
                ->where('status', '>=', 400)
                ->count()
        ];

        $crawler->update([
            'crawler_stats' => $stats,
        ]);

        $crawler
            ->urls()
            ->where('status', '=', 200)
            ->whereDoesntHave('foundOn');
    }
}
