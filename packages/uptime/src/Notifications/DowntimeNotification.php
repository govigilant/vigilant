<?php

namespace Vigilant\Uptime\Notifications;

use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;
use Vigilant\Uptime\Models\Monitor;

class DowntimeNotification extends Notification implements HasSite
{
    public static string $name = 'Site downtime detected';

    public Level $level = Level::Critical;

    public function __construct(
        public Monitor $monitor
    ) {
    }

    public function title(): string
    {
        $site = $this->monitor->site?->url ?? $this->monitor->settings['host'] ?? '';

        return __(':site is down!', ['site' => $site]);
    }

    public function uniqueId(): string
    {
        return (string) $this->monitor->id;
    }

    public function site(): ?Site
    {
        return $this->monitor->site;
    }
}
