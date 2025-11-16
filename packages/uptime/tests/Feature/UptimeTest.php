<?php

namespace Vigilant\Uptime\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Vigilant\Uptime\Commands\CheckUptimeCommand;
use Vigilant\Uptime\Enums\Type;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\Result;
use Vigilant\Uptime\Tests\TestCase;

class UptimeTest extends TestCase
{
    public function test_it_checks_uptime_via_http(): void
    {
        $monitor = null;

        /** @var Monitor $monitor */
        $monitor = Monitor::query()->create([
            'team_id' => 1,
            'name' => 'Test Monitor',
            'type' => Type::Http,
            'settings' => [
                'host' => 'http://service',
            ],
            'interval' => '* * * * *',
            'retries' => 1,
            'timeout' => 1,
        ]);

        $outpost = \Vigilant\Uptime\Models\Outpost::create([
            'ip' => '127.0.0.1',
            'port' => 3000,
            'external_ip' => '127.0.0.1',
            'status' => \Vigilant\Uptime\Enums\OutpostStatus::Available,
            'country' => 'US',
            'last_available_at' => now(),
        ]);

        Http::fake([
            'https://127.0.0.1:3000/*' => Http::response([
                'up' => true,
                'latency_ms' => 100,
            ]),
        ]);

        $this->artisan(CheckUptimeCommand::class, [
            'monitorId' => $monitor->id,
        ]);

        $this->artisan(CheckUptimeCommand::class, [
            'monitorId' => $monitor->id,
        ]);

        $results = $monitor->results;

        $this->assertCount(2, $results);
    }

    public function test_it_checks_uptime_via_ping(): void
    {
        $monitor = null;

        /** @var Monitor $monitor */
        $monitor = Monitor::query()->create([
            'team_id' => 1,
            'name' => 'Test Monitor',
            'type' => Type::Ping,
            'settings' => [
                'host' => '127.0.0.1',
                'port' => 53,
            ],
            'interval' => '* * * * *',
            'retries' => 1,
            'timeout' => 1,
        ]);

        $outpost = \Vigilant\Uptime\Models\Outpost::create([
            'ip' => '127.0.0.1',
            'port' => 3000,
            'external_ip' => '127.0.0.1',
            'status' => \Vigilant\Uptime\Enums\OutpostStatus::Available,
            'country' => 'US',
            'last_available_at' => now(),
        ]);

        Http::fake([
            'https://127.0.0.1:3000/*' => Http::response([
                'up' => true,
                'latency_ms' => 10,
            ]),
        ]);

        $this->artisan(CheckUptimeCommand::class, [
            'monitorId' => $monitor->id,
        ]);
        /** @var Result $result */
        $result = $monitor->results->first();

        $this->assertEquals(10, $result->total_time);
    }
}
