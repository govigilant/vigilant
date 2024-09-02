<?php

namespace Vigilant\Notifications\Notifications;

use Vigilant\Notifications\Enums\Level;

class TestNotification extends Notification
{
    public static string $name = 'Test Notification';

    public string $description = 'If you receive this notification it means that the notification channel is working!';

    public function __construct(public Level $level = Level::Success)
    {
    }

    public function title(): string
    {
        return 'Test Notification for level '.$this->level->name;
    }

    public function viewUrl(): ?string
    {
        return route('notifications');
    }

    public function url(): ?string
    {
        return route('notifications');
    }

    public function urlTitle(): ?string
    {
        return __('Details');
    }

    /** @codeCoverageIgnore */
    public function uniqueId(): string
    {
        return $this->level->value;
    }
}
