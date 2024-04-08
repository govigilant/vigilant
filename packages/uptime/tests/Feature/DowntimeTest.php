<?php

namespace Vigilant\Uptime\Tests\Feature;

use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Vigilant\Uptime\Commands\CheckUptimeCommand;
use Vigilant\Uptime\Data\UptimeResult;
use Vigilant\Uptime\Enums\Type;
use Vigilant\Uptime\Events\DowntimeEndEvent;
use Vigilant\Uptime\Events\DowntimeStartEvent;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Tests\TestCase;
use Vigilant\Uptime\Uptime\Http;

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
                'retries' => 1,
                'timeout' => 1,
            ]);
        });

        $this->mock(Http::class, function (MockInterface $mock) {
            $mock->shouldReceive('process')->andReturn(new UptimeResult(false));
        });

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
                'retries' => 1,
                'timeout' => 1,
            ]);
        });

        $monitor->downtimes()->create([
            'start' => now()->subMinutes(5)
        ]);

        $this->mock(Http::class, function (MockInterface $mock) {
            $mock->shouldReceive('process')->andReturn(new UptimeResult(true));
        });

        $this->artisan(CheckUptimeCommand::class, [
            'monitorId' => $monitor->id,
        ]);

        Event::assertDispatched(DowntimeEndEvent::class);

        $this->assertNotNull($monitor->downtimes()->whereNotNull('end')->first());
    }
}
