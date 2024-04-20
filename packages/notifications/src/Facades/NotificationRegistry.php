<?php

namespace Vigilant\Notifications\Facades;

use Illuminate\Support\Facades\Facade;
use Vigilant\Notifications\Notifications\NotificationRegistry as Registry;

/**
 * @method static Registry registerNotification(string|array $notifications)
 * @method static Registry registerChannel(string|array $channel)
 * @method static Registry registerCondition(string $notification, string|array $condition)
 * @method static array<int, class-string<Notification>> notifications()
 * @method static array channels()
 * @method static array conditions(string $notification)
 * @method static bool hasCondition(string $notification, string $condition)
 * @method static void fake()
 */
class NotificationRegistry extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Registry::class;
    }
}
