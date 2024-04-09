<?php

namespace Tests\Browser\Notifications;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;
use Vigilant\Notifications\Channels\NtfyChannel;
use Vigilant\Notifications\Models\Channel;

class ChannelsIndexTest extends DuskTestCase
{
    #[Test]
    public function it_shows_table(): void
    {
        $this->browse(function (Browser $browser) {

            $this->user();

            Channel::query()->create([
                'channel' => NtfyChannel::class,
                'settings' => [],
            ]);

            $browser->login()
                ->visit(route('notifications.channels'))
                ->assertSee('Ntfy');
        });
    }
}
