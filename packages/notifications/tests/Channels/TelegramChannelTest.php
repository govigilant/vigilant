<?php

namespace Vigilant\Notifications\Tests\Channels;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Notifications\Channels\TelegramChannel;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Tests\Fakes\FakeNotification;
use Vigilant\Notifications\Tests\TestCase;

class TelegramChannelTest extends TestCase
{
    #[Test]
    public function it_sends_to_telegram(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(),
        ]);

        Channel::withoutEvents(function () {
            Channel::query()->create([
                'team_id' => 1,
                'channel' => TelegramChannel::class,
                'settings' => [
                    'bot_token' => 'test_bot_token',
                    'chat_id' => '123456789',
                ],
            ]);
        });

        $notification = FakeNotification::make(1);
        /** @var Channel $channelModel */
        $channelModel = Channel::query()->withoutGlobalScopes()->first();

        /** @var TelegramChannel $channel */
        $channel = app(TelegramChannel::class);

        $channel->fire($notification, $channelModel);

        Http::assertSent(function (Request $request): bool {
            $body = $request->data();

            return $request->url() === 'https://api.telegram.org/bottest_bot_token/sendMessage' &&
                $body['chat_id'] === '123456789' &&
                $body['text'] === "*Title of this fake notification*\n\nDescription of this fake notification" &&
                $body['parse_mode'] === 'MarkdownV2';
        });
    }

    #[Test]
    public function it_escapes_markdown_v2_special_characters(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(),
        ]);

        Channel::withoutEvents(function () {
            Channel::query()->create([
                'team_id' => 1,
                'channel' => TelegramChannel::class,
                'settings' => [
                    'bot_token' => 'test_bot_token',
                    'chat_id' => '123456789',
                ],
            ]);
        });

        $notification = new class(1) extends FakeNotification
        {
            public string $title = 'Alert: CPU_usage > 90% [critical]';

            public string $description = 'Host 192.168.1.1 is down. Check #monitoring.';
        };

        /** @var Channel $channelModel */
        $channelModel = Channel::query()->withoutGlobalScopes()->first();

        /** @var TelegramChannel $channel */
        $channel = app(TelegramChannel::class);

        $channel->fire($notification, $channelModel);

        Http::assertSent(function (Request $request): bool {
            $body = $request->data();

            return $body['text'] === "*Alert: CPU\_usage \> 90% \[critical\]*\n\nHost 192\.168\.1\.1 is down\. Check \#monitoring\." &&
                $body['parse_mode'] === 'MarkdownV2';
        });
    }

    #[Test]
    public function it_sends_telegram_message_with_inline_keyboard(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response(),
        ]);

        Channel::withoutEvents(function () {
            Channel::query()->create([
                'team_id' => 1,
                'channel' => TelegramChannel::class,
                'settings' => [
                    'bot_token' => 'test_bot_token',
                    'chat_id' => '123456789',
                ],
            ]);
        });

        $notification = new class(1) extends FakeNotification
        {
            public function viewUrl(): string
            {
                return 'https://example.com/view';
            }

            public function url(): string
            {
                return 'https://example.com/action';
            }

            public function urlTitle(): string
            {
                return 'Take Action';
            }
        };

        /** @var Channel $channelModel */
        $channelModel = Channel::query()->withoutGlobalScopes()->first();

        /** @var TelegramChannel $channel */
        $channel = app(TelegramChannel::class);

        $channel->fire($notification, $channelModel);

        Http::assertSent(function (Request $request): bool {
            $body = $request->data();

            return $request->url() === 'https://api.telegram.org/bottest_bot_token/sendMessage' &&
                isset($body['reply_markup']['inline_keyboard']) &&
                count($body['reply_markup']['inline_keyboard']) === 2 &&
                $body['reply_markup']['inline_keyboard'][0][0]['text'] === 'View in Vigilant' &&
                $body['reply_markup']['inline_keyboard'][0][0]['url'] === 'https://example.com/view' &&
                $body['reply_markup']['inline_keyboard'][1][0]['text'] === 'Take Action' &&
                $body['reply_markup']['inline_keyboard'][1][0]['url'] === 'https://example.com/action';
        });
    }
}
