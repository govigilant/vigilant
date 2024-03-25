<?php

namespace Vigilant\Uptime\Notifications;

use Vigilant\Notifications\Notifications\Notification;

class DowntimeNotification extends Notification
{
    public string $key = 'downtime';
}
