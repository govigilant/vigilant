<?php

namespace Vigilant\Notifications\Tests\Channels;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Notifications\Channels\NtfyChannel;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Tests\Fakes\FakeNotification;
use Vigilant\Notifications\Tests\TestCase;

class NtfyChannelTest extends TestCase
{
    #[Test]
    public function it_sends_to_ntfy(): void
    {
        Http::fake([
            'ntfy/topic' => Http::response(),
        ]);

        Channel::withoutEvents(function() {
            Channel::query()->create([
                'team_id' => 1,
                'channel' => NtfyChannel::class,
                'settings' => [
                    'server' => 'ntfy',
                    'topic' => 'topic',
                    'auth_method' => 'username',
                    'username' => 'username',
                    'password' => 'password',
                ],
            ]);
        });

        $notification = FakeNotification::make(1);
        /** @var Channel $channelModel */
        $channelModel = Channel::query()->first();

        /** @var NtfyChannel $channel */
        $channel = app(NtfyChannel::class);

        $channel->fire($notification, $channelModel);

        Http::assertSent(function(Request $request): bool {
            return $request->header('Authorization') === ['Basic dXNlcm5hbWU6cGFzc3dvcmQ='] &&
                $request->header('Title') === ['Title of this fake notification'] &&
                $request->header('Tags') === ['triangular_flag_on_post'] &&
                $request->data() === ['Description_of_this_fake_notification' => ''];
        });
    }
}
