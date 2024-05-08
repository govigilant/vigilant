<?php

namespace Vigilant\Uptime\Notifications;

use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;
use Vigilant\Uptime\Models\Downtime;
use Vigilant\Uptime\Models\Monitor;

class DowntimeEndNotification extends Notification implements HasSite
{
    public static string $name = 'Downtime solved';

    public Level $level = Level::Critical;

    public function __construct(
        public Monitor $monitor
    ) {
    }

    public function title(): string
    {
        $site = $this->monitor->site?->url ?? $this->monitor->settings['host'] ?? '';

        return __(':site is back up!', ['site' => $site]);
    }

    public function description(): string
    {
        /** @var ?Downtime $downtime */
        $downtime = $this->monitor->downtimes()->orderByDesc('end')->first();

        if ($downtime === null) {
            return '';
        }

        return __('When down at :start and became available on :end. Downtime: :downtime', [
            'start' => $downtime->start->toDateTimeString(),
            'end' => $downtime->end?->toDateTimeString() ?? __('Unknown'),
            'downtime' => $downtime->start->diffForHumans($downtime->end),
        ]);
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
