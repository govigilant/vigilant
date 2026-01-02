<?php

namespace Vigilant\Notifications\Tests\Models;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Notifications\Channels\SlackChannel;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Tests\TestCase;

class ChannelTest extends TestCase
{
    #[Test]
    public function it_returns_the_internal_name_when_available(): void
    {
        $channel = Channel::withoutEvents(function () {
            return Channel::query()->withoutGlobalScopes()->create([
                'team_id' => 1,
                'channel' => SlackChannel::class,
                'name' => 'Primary Slack',
                'settings' => [],
            ]);
        });

        $this->assertSame('Primary Slack', $channel->title());
    }

    #[Test]
    public function it_falls_back_to_the_channel_display_name(): void
    {
        $channel = Channel::withoutEvents(function () {
            return Channel::query()->withoutGlobalScopes()->create([
                'team_id' => 1,
                'channel' => SlackChannel::class,
                'settings' => [],
            ]);
        });

        $this->assertSame(SlackChannel::$name, $channel->title());
    }
}
