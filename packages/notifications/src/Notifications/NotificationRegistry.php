<?php

namespace Vigilant\Notifications\Notifications;

use Illuminate\Support\Arr;
use Vigilant\Notifications\Channels\NotificationChannel;

class NotificationRegistry
{
    protected array $notifications = [];

    protected array $channels = [];

    /**
     * @param  class-string<Notification>|array<int, class-string<Notification>>  $notification
     */
    public function registerNotification(string|array $notification): static
    {
        $this->notifications = array_merge($this->notifications(), Arr::wrap($notification));

        return $this;
    }

    /**
     * @param  class-string<NotificationChannel>|array<int, class-string<NotificationChannel>>  $channel
     */
    public function registerChannel(string|array $channel): static
    {
        $this->channels = array_merge($this->channels(), Arr::wrap($channel));

        return $this;
    }

    public function notifications(): array
    {
        return $this->notifications;
    }

    public function channels(): array
    {
        return $this->channels;
    }
}
