<?php

namespace Vigilant\Notifications\Channels;

use Illuminate\Support\Facades\Http;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Notifications\Notification;

class NtfyChannel extends NotificationChannel
{
    public static string $name = 'Ntfy';

    public static ?string $component = 'channel-configuration-ntfy';

    public array $rules = [
        'server' => ['required', 'url'],
        'topic' => ['required'],
        'auth_method' => ['nullable', 'in:username,token'],
        'username' => ['required_if:auth_method,username'],
        'password' => ['required_if:auth_method,username'],
        'token' => ['required_if:auth_method,token'],
    ];

    public function fire(Notification $notification, Channel $channel): void
    {
        $settings = $channel->settings;

        $tag = match ($notification->level()) {
            Level::Info => 'grey_exclamation',
            Level::Warning => 'warning',
            Level::Critical => 'triangular_flag_on_post',
            Level::Success => 'white_check_mark',
        };

        $request = Http::baseUrl($settings['server'])
            ->withHeaders([
                'Title' => $notification->title() . ' - ' . config('app.name'),
                'Tags' => $tag,
            ]);

        $viewUrl = $notification->viewUrl();
        $url = $notification->url();
        $urlTitle = $notification->urlTitle();

        if ($viewUrl !== null) {
            $request->withHeader('click', $viewUrl);
        }

        if ($url !== null && $urlTitle !== null) {
            $request->withHeader('Actions', "view, $urlTitle, $url, clear=true");
        }

        if ($settings['auth_method'] === 'username') {
            $request->withBasicAuth(
                $settings['username'],
                $settings['password'],
            );
        }

        if ($settings['auth_method'] === 'token') {
            $request->withToken($settings['token']);
        }

        $request->post($settings['topic'], $notification->description()); // @phpstan-ignore-line
    }
}
