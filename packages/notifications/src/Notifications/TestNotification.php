<?php

namespace Vigilant\Notifications\Notifications;

use Vigilant\Notifications\Enums\Level;

class TestNotification extends Notification
{
    public static string $name = 'Test Notification';

    public string $title = 'Test Notification';

    public string $description = 'If you receive this notification it means that the notification channel is working!';

    public Level $level = Level::Success;

    /** @codeCoverageIgnore */
    public function uniqueId(): string
    {
        return '';
    }
}
