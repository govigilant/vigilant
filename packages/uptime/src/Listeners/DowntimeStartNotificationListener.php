<?php

namespace Vigilant\Uptime\Listeners;

use Vigilant\Uptime\Events\DowntimeStartEvent;
use Vigilant\Uptime\Notifications\DowntimeStartNotification;

class DowntimeStartNotificationListener
{
    public function handle(DowntimeStartEvent $event): void
    {
       DowntimeStartNotification::notify($event->monitor);
    }
}
