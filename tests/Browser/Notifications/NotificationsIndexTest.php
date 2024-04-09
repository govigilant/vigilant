<?php

namespace Tests\Browser\Notifications;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;
use Vigilant\Notifications\Models\Trigger;
use Vigilant\Uptime\Notifications\DowntimeNotification;

class NotificationsIndexTest extends DuskTestCase
{
    #[Test]
    public function it_shows_table(): void
    {
        $this->browse(function (Browser $browser) {

            $this->user();

            Trigger::query()->create([
                'notification' => DowntimeNotification::class,
                'conditions' => [],
                'all_channels' => false,
            ]);

            $browser->login()
                ->visit(route('notifications'))
                ->assertSee(DowntimeNotification::$name);
        });
    }
}
