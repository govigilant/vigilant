<?php

namespace Vigilant\Healthchecks\Enums;

use Vigilant\Healthchecks\Checks\Checker;
use Vigilant\Healthchecks\Checks\Endpoint;
use Vigilant\Healthchecks\Checks\Module;

enum Type: string
{
    case Endpoint = 'endpoint';
    case Module = 'module';

    public function label(): string
    {
        return match ($this) {
            self::Endpoint => __('Endpoint'),
            self::Module => __('Module'),
        };
    }

    public function checker(): Checker
    {
        $class = match ($this) {
            self::Endpoint => Endpoint::class,
            self::Module => Module::class,
        };

        return app($class);
    }
}
