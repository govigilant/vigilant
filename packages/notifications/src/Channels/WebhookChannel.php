<?php

namespace Vigilant\Notifications\Channels;

use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Models\Trigger;

class WebhookChannel extends NotificationChannel
{
    public static string $name = 'Webhook';

    public static ?string $component = 'channel-configuration-webhook';

    public array $rules = [
        'url' => ['required', 'url'],
    ];

    public function fire(Channel $channel, Trigger $trigger): void
    {
        dd('webhook!');
    }
}
