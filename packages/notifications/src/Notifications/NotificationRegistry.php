<?php

namespace Vigilant\Notifications\Notifications;

use Illuminate\Support\Arr;

class NotificationRegistry
{
    protected array $notifications = [];

    /**
     * @param  class-string<Notification>|array<int, class-string<Notification>>  $notification
     */
    public function register(string|array $notification): static
    {
        $this->notifications = array_merge($this->notifications(), Arr::wrap($notification));

        return $this;
    }

    public function notifications(): array
    {
       return $this->notifications;
    }
}
