<?php

namespace Vigilant\Uptime\Enums;

use Vigilant\Uptime\Uptime\Http;
use Vigilant\Uptime\Uptime\Ping;
use Vigilant\Uptime\Uptime\UptimeMonitor;

enum Type: string
{
    case Http = 'http';
    case Ping = 'ping';

    public function label(): string
    {
        return match ($this) {
            Type::Http => 'HTTP',
            Type::Ping => 'Ping',
        };
    }

    public function monitor(): UptimeMonitor
    {
        $class = match ($this) {
            Type::Http => Http::class,
            Type::Ping => Ping::class,
        };

        /** @var UptimeMonitor $instance */
        $instance = app($class);

        return $instance;
    }
}
