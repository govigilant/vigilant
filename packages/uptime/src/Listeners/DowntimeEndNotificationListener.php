<?php

namespace Vigilant\Uptime\Listeners;

use Vigilant\Uptime\Events\DowntimeStartEvent;
use Vigilant\Uptime\Notifications\DowntimeEndNotification;

class DowntimeEndNotificationListener
{
    public function handle(DowntimeStartEvent $event): void
    {
        DowntimeEndNotification::notify($event->monitor);
    }
}
