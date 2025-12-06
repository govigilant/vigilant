<?php

namespace Vigilant\Crawler\Notifications;

use Vigilant\Crawler\Models\Crawler;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class RatelimitedNotification extends Notification implements HasSite
{
    public static string $name = 'Crawler Ratelimited';

    public Level $level = Level::Warning;

    public static ?int $defaultCooldown = 60;

    public function __construct(public Crawler $crawler) {}

    public function title(): string
    {
        return __('Crawler failed due to ratelimits');
    }

    public static function info(): ?string
    {
        return __('Triggered when the crawler is blocked by rate limiting on the target site.');
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
