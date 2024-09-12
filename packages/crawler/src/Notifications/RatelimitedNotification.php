<?php

namespace Vigilant\Crawler\Notifications;

use Vigilant\Crawler\Models\Crawler;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class RatelimitedNotification extends Notification implements HasSite
{
    public static string $name = 'Crawler Ratelimited';

    public function __construct(public Crawler $crawler)
    {
    }

    public function title(): string
    {
        return __('Crawler failed due to ratelimits');
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
