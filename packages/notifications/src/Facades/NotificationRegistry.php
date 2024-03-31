<?php

namespace Vigilant\Notifications\Facades;

use Illuminate\Support\Facades\Facade;
use Vigilant\Notifications\Notifications\NotificationRegistry as Registry;

/**
 * @method Registry registerNotification(string|array $notifications)
 * @method Registry registerChannel(string|array $channel)
 * @method array notifications()
 * @method array channels()
 */
class NotificationRegistry extends Facade
{
   protected static function getFacadeAccessor(): string
   {
       return Registry::class;
   }
}
