<?php

namespace Vigilant\Notifications\Tests\Notifications;

use Vigilant\Notifications\Facades\NotificationRegistry;
use Vigilant\Notifications\Tests\Fakes\FakeNotification;
use Vigilant\Notifications\Tests\TestCase;

class NotificationRegistryTest extends TestCase
{
    public function test_it_can_register_notification(): void
    {
        NotificationRegistry::register(FakeNotification::class);

        $this->assertEquals([
            FakeNotification::class
        ], NotificationRegistry::notifications());
    }
}
