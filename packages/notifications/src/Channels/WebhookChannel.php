<?php

namespace Vigilant\Notifications\Channels;

use Vigilant\Core\Http\Exceptions\SsrfException;
use Vigilant\Core\Http\SsrfGuard;
use Vigilant\Core\Validation\NotInternalUrl;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Notifications\Notification;

class WebhookChannel extends NotificationChannel
{
    public static string $name = 'Webhook';

    public static ?string $component = 'channel-configuration-webhook';

    public array $rules = [];

    public function rules(): array
    {
        return [
            'url' => ['required', 'url', new NotInternalUrl],
        ];
    }

    public function fire(Notification $notification, Channel $channel): void
    {
        /** @var SsrfGuard $guard */
        $guard = app(SsrfGuard::class);
        $url = $channel->settings['url'];

        try {
            $guard->assertSafeUrl($url);
        } catch (SsrfException) {
            return;
        }

        $guard->request($url)->post($url, [
            'level' => $notification->level(),
            'title' => $notification->title(),
            'description' => $notification->description(),
        ]);
    }
}
