<?php

namespace Vigilant\Uptime\Notifications;

use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Uptime\Models\Monitor;

class DowntimeNotification extends Notification
{
    public static string $name = 'Site downtime detected';

    public function __construct(
        public Monitor $monitor
    ) {
    }

    public function uniqueId(): string
    {
        return $this->monitor->id;
    }
}
