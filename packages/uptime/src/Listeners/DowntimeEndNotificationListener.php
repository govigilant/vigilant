<?php

namespace Vigilant\Uptime\Listeners;

use Vigilant\Uptime\Events\DowntimeEndEvent;
use Vigilant\Uptime\Notifications\DowntimeEndNotification;

class DowntimeEndNotificationListener
{
    public function handle(DowntimeEndEvent $event): void
    {
        DowntimeEndNotification::notify($event->downtime);
    }
}
