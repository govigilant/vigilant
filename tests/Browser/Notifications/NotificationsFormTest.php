<?php

namespace Tests\Browser\Notifications;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;
use Vigilant\Notifications\Models\Trigger;
use Vigilant\Uptime\Notifications\DowntimeNotification;

class NotificationsFormTest extends DuskTestCase
{
    #[Test]
    public function it_can_add_channel(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('notifications'))
                ->click('@trigger-add-button')
                ->pause(10 * 1000)
                ->assertSee('Choose the event that triggers this notification') // Help text of the trigger dropdown
                ->select('#form\.notification', DowntimeNotification::class)
                ->check('#form\.all_channels')
                ->click('@submit-button')
                ->pause(250);

            /** @var ?Trigger $trigger */
            $trigger = Trigger::query()->first();
            $this->assertNotNull($trigger);
            $this->assertEquals(DowntimeNotification::class, $trigger->notification);
            $this->assertTrue($trigger->all_channels);
        });
    }
}
