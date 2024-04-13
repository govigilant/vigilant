<?php

namespace Tests\Browser\Notifications;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;
use Vigilant\Uptime\Notifications\DowntimeNotification;

class NotificationsIndexTest extends DuskTestCase
{
    #[Test]
    public function it_shows_table(): void
    {
        $this->browse(function (Browser $browser) {

            $this->user();

            $browser->login()
                ->visit(route('notifications'))
                ->assertSee(DowntimeNotification::$name);
        });
    }
}
