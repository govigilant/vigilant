<?php

namespace Vigilant\Notifications\Tests\Fakes;

use Vigilant\Notifications\Channels\NotificationChannel;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Notifications\Notification;

class FakeChannel extends NotificationChannel
{
    public function fire(Notification $notification, Channel $channel): void
    {
        //
    }
}
