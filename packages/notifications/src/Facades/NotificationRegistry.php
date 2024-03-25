<?php

namespace Vigilant\Notifications\Facades;

use Illuminate\Support\Facades\Facade;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Notifications\Notifications\NotificationRegistry as Registry;

/**
 * @method Registry register(string $notification)
 * @method array notifications()
 */
class NotificationRegistry extends Facade
{
   protected static function getFacadeAccessor(): string
   {
       return Registry::class;
   }
}
