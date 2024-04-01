<?php

namespace Vigilant\Notifications\Channels;

use Illuminate\Support\Facades\Http;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Notifications\Notification;

class WebhookChannel extends NotificationChannel
{
    public static string $name = 'Webhook';

    public static ?string $component = 'channel-configuration-webhook';

    public array $rules = [
        'url' => ['required', 'url'],
    ];

    public function fire(Notification $notification, Channel $channel): void
    {
        Http::post($channel->settings['url'], [
            'level' => $notification->level(),
            'title' => $notification->title(),
            'description' => $notification->description()
        ]);
    }
}
