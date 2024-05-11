<?php

namespace Vigilant\Uptime\Notifications;

use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;
use Vigilant\Uptime\Models\Downtime;

class DowntimeEndNotification extends Notification implements HasSite
{
    public static string $name = 'Downtime solved';

    public Level $level = Level::Success;

    public function __construct(
        public Downtime $downtime
    ) {
    }

    public function title(): string
    {
        $monitor = $this->downtime->monitor;

        $site = $monitor->site?->url ?? $monitor->settings['host'] ?? '';

        return __(':site is back up!', ['site' => $site]);
    }

    public function description(): string
    {
        return __('When down at :start and became available on :end. Downtime: :downtime', [
            'start' => $this->downtime->start->toDateTimeString(),
            'end' => $this->downtime->end?->toDateTimeString() ?? __('Unknown'),
            'downtime' => $this->downtime->start->longAbsoluteDiffForHumans($this->downtime->end),
        ]);
    }

    public function uniqueId(): string|int
    {
        return $this->downtime->id;
    }

    public function site(): ?Site
    {
        return $this->downtime->monitor?->site;
    }
}
