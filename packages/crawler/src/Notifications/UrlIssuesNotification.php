<?php

namespace Vigilant\Crawler\Notifications;

use Vigilant\Crawler\Models\Crawler;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class UrlIssuesNotification extends Notification implements HasSite
{
    public static string $name = 'URLs with issues found';

    public Level $level = Level::Critical;

    public function __construct(public Crawler $crawler) {}

    public function title(): string
    {
        /** @var int $count */
        $count = $this->crawler->crawler_stats['issue_count'] ?? 0;

        return __(':count URL\'s found with issues on :site', [
            'count' => $count,
            'site' => $this->site()?->url ?? $this->crawler->start_url,
        ]);
    }

    public function viewUrl(): ?string
    {
        return route('crawler.view', ['crawler' => $this->crawler]);
    }

    public function site(): ?Site
    {
        return $this->crawler->site;
    }

    public function uniqueId(): string|int
    {
        return $this->crawler->id;
    }
}
