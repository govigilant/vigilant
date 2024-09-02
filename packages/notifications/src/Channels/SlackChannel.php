<?php

namespace Vigilant\Notifications\Channels;

use Illuminate\Support\Facades\Http;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Notifications\Notification;

class SlackChannel extends NotificationChannel
{
    public static string $name = 'Slack';

    public static ?string $component = 'channel-configuration-slack';

    public array $rules = [
        'webhook_url' => ['required', 'url'],
    ];

    public function fire(Notification $notification, Channel $channel): void
    {
        $settings = $channel->settings;

        $blocks = [];

        $blocks[] = [
            'type' => 'section',
            'text' => [
                'type' => 'mrkdwn',
                'text' => "*{$notification->title()}*\n{$notification->description()}",
            ],
        ];

        if ($viewUrl = $notification->viewUrl()) {
            $blocks[] = [
                'type' => 'actions',
                'elements' => [
                    [
                        'type' => 'button',
                        'text' => [
                            'type' => 'plain_text',
                            'text' => __('View in Vigilant'),
                            'emoji' => true,
                        ],
                        'url' => $viewUrl,
                        'action_id' => 'view_more_button',
                    ],
                ],
            ];
        }

        if (($url = $notification->url()) && ($urlTitle = $notification->urlTitle())) {
            $blocks[] = [
                'type' => 'actions',
                'elements' => [
                    [
                        'type' => 'button',
                        'text' => [
                            'type' => 'plain_text',
                            'text' => __($urlTitle),
                            'emoji' => true,
                        ],
                        'url' => $url,
                        'action_id' => 'action_button',
                    ],
                ],
            ];
        }

        $payload = [
            'blocks' => $blocks,
            'attachments' => [
                [
                    'color' => $notification->level()->color(),
                ],
            ],
        ];

        Http::post($settings['webhook_url'], $payload);
    }
}
