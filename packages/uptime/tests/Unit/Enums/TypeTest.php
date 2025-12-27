<?php

namespace Vigilant\Uptime\Tests\Unit\Enums;

use Vigilant\Uptime\Enums\Type;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Tests\TestCase;

class TypeTest extends TestCase
{
    public function test_http_format_target_accepts_ip_host(): void
    {
        $monitor = Monitor::factory()->make([
            'settings' => ['host' => '127.0.0.1'],
            'type' => Type::Http,
        ]);

        $this->assertSame('127.0.0.1', Type::Http->formatTarget($monitor));
    }
}
