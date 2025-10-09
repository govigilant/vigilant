<?php

namespace Vigilant\Uptime\Tests\Feature;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Vigilant\Uptime\Commands\CheckUptimeCommand;
use Vigilant\Uptime\Enums\Type;
use Vigilant\Uptime\Events\DowntimeEndEvent;
use Vigilant\Uptime\Events\DowntimeStartEvent;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Tests\TestCase;

class DowntimeTest extends TestCase
{
    public function test_it_checks_dispatches_event_on_downtime_once(): void
    {
        Event::fake();

        $monitor = null;

        Monitor::withoutEvents(function () use (&$monitor) {
            /** @var Monitor $monitor */
            $monitor = Monitor::query()->create([
                'team_id' => 1,
                'name' => 'Test Monitor',
                'type' => Type::Http,
                'settings' => [
                    'host' => 'http://service',
                ],
                'interval' => '* * * * *',
                'retries' => 0,
                'timeout' => 1,
            ]);
        });

        $this->assertNotNull($monitor);

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
                'up' => false,
                'latency_ms' => 0,
            ]),
        ]);

        $this->artisan(CheckUptimeCommand::class, [
            'monitorId' => $monitor->id,
        ]);

        $this->artisan(CheckUptimeCommand::class, [
            'monitorId' => $monitor->id,
        ]);

        Event::assertDispatchedTimes(DowntimeStartEvent::class, 1);
    }

    public function test_it_resolves_downtime(): void
    {
        Event::fake();

        $monitor = null;

        Monitor::withoutEvents(function () use (&$monitor) {
            /** @var Monitor $monitor */
            $monitor = Monitor::query()->create([
                'team_id' => 1,
                'name' => 'Test Monitor',
                'type' => Type::Http,
                'settings' => [
                    'host' => 'http://service',
                ],
                'interval' => '* * * * *',
                'retries' => 0,
                'timeout' => 1,
            ]);
        });

        $this->assertNotNull($monitor);

        $monitor->downtimes()->create([
            'start' => now()->subMinutes(5),
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

        Event::assertDispatched(DowntimeEndEvent::class);

        $this->assertNotNull($monitor->downtimes()->whereNotNull('end')->first());
    }
}
