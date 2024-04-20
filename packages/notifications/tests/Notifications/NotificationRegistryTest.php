<?php

namespace Vigilant\Notifications\Tests\Notifications;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Notifications\Facades\NotificationRegistry;
use Vigilant\Notifications\Tests\Fakes\FakeChannel;
use Vigilant\Notifications\Tests\Fakes\FakeNotification;
use Vigilant\Notifications\Tests\TestCase;

class NotificationRegistryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        NotificationRegistry::fake();
    }

    #[Test]
    public function it_can_register_notification(): void
    {
        NotificationRegistry::registerNotification(FakeNotification::class);

        $this->assertEquals([
            FakeNotification::class,
        ], NotificationRegistry::notifications());
    }

    #[Test]
    public function it_can_register_channels(): void
    {
        NotificationRegistry::registerChannel(FakeChannel::class);

        $this->assertEquals([
            FakeChannel::class,
        ], NotificationRegistry::channels());
    }
}
