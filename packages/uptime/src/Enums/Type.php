<?php

namespace Vigilant\Uptime\Enums;

use Illuminate\Support\Facades\Validator;
use Vigilant\Uptime\Models\Monitor;

enum Type: string
{
    case Http = 'http';
    case Ping = 'icmp';
    case Tcp = 'tcp';

    public function label(): string
    {
        return match ($this) {
            Type::Http => 'HTTP(s)',
            Type::Ping => 'Ping',
            Type::Tcp => 'TCP',
        };
    }

    public function outpostValue(): string
    {
        return match ($this) {
            Type::Http => 'http',
            Type::Ping => 'icmp',
            Type::Tcp => 'tcp',
        };
    }

    public function formatTarget(Monitor $monitor): string
    {
        if ($this === Type::Http) {
            $settings = Validator::validate($monitor->settings, [
                'host' => ['required', 'url'],
            ]);

            return $settings['host'];
        }

        if ($this === Type::Ping) {
            $settings = Validator::validate($monitor->settings, [
                'host' => ['required', 'ip'],
            ]);

            return $settings['host'];
        }

        $settings = Validator::validate($monitor->settings, [
            'host' => ['required', 'ip'],
            'port' => ['integer', 'min:1', 'max:65535'],
        ]);

        return sprintf('%s:%s', $settings['host'], $settings['port']);
    }
}
