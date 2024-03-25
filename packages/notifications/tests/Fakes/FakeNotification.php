<?php

namespace Vigilant\Notifications\Tests\Fakes;

use Vigilant\Notifications\Notifications\Notification;

class FakeNotification extends Notification
{
    public function __construct(
        protected int $number
    ) {
    }
}
