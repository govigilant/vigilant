<?php

namespace Vigilant\Notifications\Channels;

use Illuminate\Support\Facades\Http;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Notifications\Notification;

class TelegramChannel extends NotificationChannel
{
    public static string $name = 'Telegram';

    public static ?string $component = 'channel-configuration-telegram';

    public array $rules = [
        'bot_token' => ['required', 'string'],
        'chat_id' => ['required', 'string'],
    ];

    public function fire(Notification $notification, Channel $channel): void
    {
        $settings = $channel->settings;

        $text = "*{$notification->title()}*\n\n{$notification->description()}";

        $inlineKeyboard = [];

        if ($viewUrl = $notification->viewUrl()) {
            $inlineKeyboard[] = [
                [
                    'text' => __('View in Vigilant'),
                    'url' => $viewUrl,
                ],
            ];
        }

        if (($url = $notification->url()) && ($urlTitle = $notification->urlTitle())) {
            $inlineKeyboard[] = [
                [
                    'text' => __($urlTitle),
                    'url' => $url,
                ],
            ];
        }

        $payload = [
            'chat_id' => $settings['chat_id'],
            'text' => $text,
            'parse_mode' => 'Markdown',
        ];

        if (! empty($inlineKeyboard)) {
            $payload['reply_markup'] = [
                'inline_keyboard' => $inlineKeyboard,
            ];
        }

        $url = "https://api.telegram.org/bot{$settings['bot_token']}/sendMessage";

        Http::post($url, $payload);
    }
}
