<?php

namespace Vigilant\Notifications\Concerns;

use Vigilant\Notifications\Notifications\Notification;

trait NotificationFake
{
    protected static bool $faked = false;
    /** @var array<int, Notification> $fakeDispatches */
    protected static array $fakeDispatches = [];

    public static function fake(): void
    {
        static::$faked = true;
    }

    public static function wasDispatched(?\Closure $callback = null): bool
    {
        if ($callback !== null) {
            foreach (static::$fakeDispatches as $dispatch) {
                if (! $callback($dispatch)) {
                    return false;
                }
            }

            return true;
        }

        return count(static::$fakeDispatches) > 0;
    }
}
