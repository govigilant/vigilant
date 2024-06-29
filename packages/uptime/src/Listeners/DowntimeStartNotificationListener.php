<?php

namespace Vigilant\Uptime\Listeners;

use Vigilant\Core\Services\TeamService;
use Vigilant\Uptime\Events\DowntimeStartEvent;
use Vigilant\Uptime\Notifications\DowntimeStartNotification;

class DowntimeStartNotificationListener
{
    public function __construct(protected TeamService $teamService) {}

    public function handle(DowntimeStartEvent $event): void
    {
        $this->teamService->setTeam($event->monitor->team);

        DowntimeStartNotification::notify($event->monitor);
    }
}
