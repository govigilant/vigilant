<?php

namespace Vigilant\Core\Facades;

use Illuminate\Support\Facades\Facade;
use Vigilant\Core\Navigation\NavigationItem;

/**
 * @method Navigation path(string $path)
 * @method NavigationItem add(string $url, string $name)
 * @method array items()
 */
class Navigation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Vigilant\Core\Navigation\Navigation::class;
    }

    public static function bind(): void
    {
        app()->bind(\Vigilant\Core\Navigation\Navigation::class, fn() => new \Vigilant\Core\Navigation\Navigation());
    }
}
