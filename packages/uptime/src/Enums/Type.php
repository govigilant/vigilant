<?php

namespace Vigilant\Uptime\Enums;

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
}
