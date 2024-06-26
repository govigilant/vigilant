<?php

namespace Vigilant\Notifications\Notifications;

use Illuminate\Support\Arr;
use Vigilant\Notifications\Channels\NotificationChannel;
use Vigilant\Notifications\Conditions\Condition;

class NotificationRegistry
{
    protected array $notifications = [];

    protected array $conditions = [];

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
     * @param  class-string<Condition>|array<int, class-string<Condition>>  $condition
     */
    public function registerCondition(string $notification, string|array $condition): static
    {
        $this->conditions[$notification] = array_merge($this->conditions($notification), Arr::wrap($condition));

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

    public function conditions(string $notification): array
    {
        return $this->conditions[$notification] ?? [];
    }

    public function hasCondition(string $notification, string $condition): bool
    {
        return in_array($condition, $this->conditions($notification));
    }

    public function fake(): void
    {
        $this->notifications = [];
        $this->channels = [];
        $this->conditions = [];
    }
}
