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
                'enabled' => true,
                'name' => 'Downtime',
                'notification' => DowntimeNotification::class,
                'conditions' => [],
            ]);

            $browser->login()
                ->visit(route('notifications'))
                ->waitForText(DowntimeNotification::$name, 5);
        });
    }
}
