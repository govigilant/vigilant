<?php

namespace Vigilant\Notifications\Tests\Notifications;

use Vigilant\Notifications\Tests\Fakes\FakeNotification;
use Vigilant\Notifications\Tests\TestCase;

class NotificationTest extends TestCase
{
    public function test_it_fires_notification(): void
    {
        FakeNotification::notify(1);
    }
}
