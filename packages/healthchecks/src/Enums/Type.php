<?php

namespace Vigilant\Healthchecks\Enums;

use Vigilant\Healthchecks\Checks\Checker;
use Vigilant\Healthchecks\Checks\Endpoint;
use Vigilant\Healthchecks\Checks\Module;

enum Type: string
{
    case Endpoint = 'endpoint';
    case Laravel = 'laravel';
    case Statamic = 'statamic';
    /* case Magento = 'magento'; */

    public function label(): string
    {
        return match ($this) {
            default => ucfirst($this->value),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Endpoint => 'phosphor-heartbeat',
            self::Laravel => 'si-laravel',
            self::Statamic => 'si-statamic',
            /* static::Magento => 'bxl-magento', */
        };
    }

    public function endpoint(): ?string
    {
        return match ($this) {
            self::Endpoint => null,
            default => 'api/vigilant/health'
        };
    }

    public function checker(): Checker
    {
        $class = match ($this) {
            self::Endpoint => Endpoint::class,
            default => Module::class,
        };

        return app($class);
    }
}
