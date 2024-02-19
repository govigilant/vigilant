<?php

namespace Vigilant\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method Navigation path(string $path)
 * @method Navigation add(string $url, string $name, string $icon, int $sort)
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
