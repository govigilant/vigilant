<?php

namespace Tests\Browser\Notifications;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;
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
                ->assertSee('Choose the event that triggers this notification') // Help text of the trigger dropdown
                ->type('#form\.name', 'Test Notification')
                ->select('#form\.notification', DowntimeNotification::class)
                ->check('#form\.all_channels')
                ->clickAndWaitForReload('@submit-button')
                ->assertPathContains('notifications/edit')
                ->assertSee('Site downtime detected');
        });
    }
}
