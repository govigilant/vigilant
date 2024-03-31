<?php

namespace Vigilant\Notifications\Notifications;

use Illuminate\Support\Collection;
use Vigilant\Notifications\Models\Trigger;

abstract class Notification
{
    public static string $name = '';

    public static function notify(...$args)
    {
        $instance = new static(...$args);

        /** @var Collection<int, Trigger> $triggers */
        $triggers = Trigger::query()
            ->where('notification', '=', static::class)
            ->get();

        foreach($triggers as $trigger) {

            // TODO: Check conditions

            // Dispatch job to send notification



        }
    }

    abstract public function uniqueId(): string;
}
