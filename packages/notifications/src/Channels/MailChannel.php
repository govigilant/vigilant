<?php

namespace Vigilant\Notifications\Channels;

use Illuminate\Support\Facades\Mail;
use Vigilant\Notifications\Mail\NotificationMail;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Notifications\Notification;

class MailChannel extends NotificationChannel
{
    public static string $name = 'Mail';

    public static ?string $component = 'channel-configuration-mail';

    public array $rules = [
        'to' => ['required', 'email'],
    ];

    public function fire(Notification $notification, Channel $channel): void
    {
        $settings = $channel->settings;

        /** @var string $to */
        $to = $settings['to'];

        Mail::to($to)->send(new NotificationMail($notification));
    }
}
