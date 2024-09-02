<?php

namespace Vigilant\Notifications\Channels;

use Illuminate\Support\Facades\Http;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Notifications\Notification;

class DiscordChannel extends NotificationChannel
{
    public static string $name = 'Discord';

    public static ?string $component = 'channel-configuration-discord';

    public array $rules = [
        'webhook_url' => ['required', 'url', 'starts_with:https://discord.com/api/webhooks'],
    ];

    public function fire(Notification $notification, Channel $channel): void
    {
        $settings = $channel->settings;

        $description = $notification->description();

        if ($viewUrl = $notification->viewUrl()) {
            $description .= __("\n\n[View in Vigilant](:url)", ['url' => $viewUrl]);
        }

        $fields = [];

        if (($url = $notification->url()) && ($urlTitle = $notification->urlTitle())) {
            $fields[] = [
                'name' => $urlTitle,
                'value' => __('[Click here](:url)', ['url' => $url]),
                'inline' => true,
            ];
        }

        $payload = [
            'embeds' => [
                [
                    'title' => $notification->title(),
                    'description' => $description,
                    'color' => $notification->level()->color(),
                    'fields' => $fields,
                ],
            ],
        ];

        Http::post($settings['webhook_url'], $payload);
    }
}
