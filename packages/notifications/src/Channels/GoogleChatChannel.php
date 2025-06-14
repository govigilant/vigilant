<?php

namespace Vigilant\Notifications\Channels;

use Illuminate\Support\Facades\Http;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Notifications\Notification;

class GoogleChatChannel extends NotificationChannel
{
    public static string $name = 'Google Chat';

    public static ?string $component = 'channel-configuration-google-chat';

    public array $rules = [
        'webhook_url' => ['required', 'url', 'starts_with:https://chat.googleapis.com/v1/spaces/'],
    ];

    public function fire(Notification $notification, Channel $channel): void
    {
        $settings = $channel->settings;

        $description = $notification->description();

        if ($viewUrl = $notification->viewUrl()) {
            $description .= "\n\n[View in Vigilant]($viewUrl)";
        }

        $lines = [];

        $lines[] = '*'.$notification->title().'*';
        $lines[] = $description;

        if (($url = $notification->url()) && ($urlTitle = $notification->urlTitle())) {
            $lines[] = "\n[".$urlTitle."]($url)";
        }

        $payload = [
            'text' => implode("\n", $lines),
        ];

        Http::post($settings['webhook_url'], $payload);
    }
}
