<?php

namespace Vigilant\Notifications\Tests\Fakes;

use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;

class FakeNotification extends Notification
{
    public static string $name = 'Fake Notification';

    public string $title = 'Title of this fake notification';

    public string $description = 'Description of this fake notification';

    public Level $level = Level::Critical;

    public function __construct(
        protected int $number
    ) {
    }

    public function uniqueId(): string
    {
        return (string) $this->number;
    }
}
