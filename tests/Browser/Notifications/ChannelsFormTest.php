<?php

namespace Tests\Browser\Notifications;

use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;
use Vigilant\Notifications\Channels\NtfyChannel;
use Vigilant\Notifications\Models\Channel;

class ChannelsFormTest extends DuskTestCase
{
    #[Test]
    public function it_can_add_channel(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(route('notifications.channels'))
                ->click('@channel-add-button')
                ->assertSee('Choose the notification channel') // Help text of channel dropdown
                ->select('#form\.channel', NtfyChannel::class)
                ->pause(250)
                ->type('#settings\.server', 'https://ntfy.govigilant.io')
                ->type('#settings\.topic', 'topic')
                ->pause(1000)
                ->click('@submit-button')
                ->pause(250);

            /** @var ?Channel $channel */
            $channel = Channel::query()->first();
            $this->assertNotNull($channel);
            $this->assertEquals(NtfyChannel::class, $channel->channel);
            $this->assertEquals(['server' => 'https://ntfy.govigilant.io', 'topic' => 'topic'], $channel->settings);
        });
    }
}
