<?php

namespace Vigilant\Crawler\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Vigilant\Core\Services\TeamService;
use Vigilant\Crawler\Models\CrawledUrl;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Crawler\Notifications\UrlIssuesNotification;

class CollectCrawlerStats
{
    public function __construct(protected TeamService $teamService) {}

    public function collect(Crawler $crawler): void
    {
        $this->teamService->setTeamById($crawler->team_id);

        /** @var Collection<int, int> $statuses */
        $statuses = $crawler
            ->urls()
            ->where('crawled', '=', true)
            ->where('ignored', '=', false)
            ->selectRaw('status, COUNT(*) AS count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $stats = [
            'total_url_count' => $statuses->sum(),
            'statuses' => $statuses,
            'issue_count' => $crawler->urls()
                ->where('crawled', '=', true)
                ->where('ignored', '=', false)
                ->where(function (Builder $query): void {
                    $query->where('status', '>=', 400)
                        ->orWhere('status', '=', 0);
                })
                ->count(),
        ];

        $crawler->update([
            'crawler_stats' => $stats,
        ]);

        $crawler
            ->urls()
            ->where('status', '=', 200)
            ->whereDoesntHave('foundOn')
            ->select(['web_crawled_urls.uuid'])
            ->lazy()
            ->chunk(1000)
            ->each(fn (LazyCollection $urls) => CrawledUrl::query()->whereIn('uuid', $urls->pluck('uuid'))->delete());

        if ($stats['issue_count'] > 0) {
            UrlIssuesNotification::notify($crawler);
        }
    }
}
