<?php

namespace Vigilant\Notifications\Channels;

use Illuminate\Support\Facades\Http;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Notifications\Notification;

class MicrosoftTeamsChannel extends NotificationChannel
{
    public static string $name = 'Microsoft Teams';

    public static ?string $component = 'channel-configuration-microsoft-teams';

    public array $rules = [
        'webhook_url' => ['required', 'url', 'starts_with:https://outlook.office.com/webhook/'],
    ];

    public function fire(Notification $notification, Channel $channel): void
    {
        $settings = $channel->settings;

        $description = $notification->description();

        if ($viewUrl = $notification->viewUrl()) {
            $description .= "\n\n[View in Vigilant]($viewUrl)";
        }

        $facts = [];

        if (($url = $notification->url()) && ($urlTitle = $notification->urlTitle())) {
            $facts[] = [
                'name' => $urlTitle,
                'value' => "[Click here]($url)",
            ];
        }

        $payload = [
            '@type' => 'MessageCard',
            '@context' => 'http://schema.org/extensions',
            'summary' => $notification->title(),
            'themeColor' => $notification->level()->color(),
            'title' => $notification->title(),
            'text' => $description,
            'sections' => count($facts) ? [
                [
                    'facts' => $facts,
                ],
            ] : [],
        ];

        Http::post($settings['webhook_url'], $payload);
    }
}
