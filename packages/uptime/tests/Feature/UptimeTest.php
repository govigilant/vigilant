<?php

namespace Vigilant\Uptime\Tests\Feature;

use Illuminate\Support\Facades\Http;
use JJG\Ping;
use Mockery\MockInterface;
use Vigilant\Uptime\Commands\CheckUptimeCommand;
use Vigilant\Uptime\Enums\Type;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\Result;
use Vigilant\Uptime\Tests\TestCase;

class UptimeTest extends TestCase
{
    public function test_it_checks_uptime_via_http(): void
    {
        /** @var Monitor $monitor */
        $monitor = Monitor::query()->create([
            'name' => 'Test Monitor',
            'type' => Type::Http,
            'settings' => [
                'host' => 'http://service',
            ],
            'interval' => '* * * * *',
            'retries' => 1,
            'timeout' => 1,
        ]);

        Http::fake([
            'http://service' => Http::response()
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
        /** @var Monitor $monitor */
        $monitor = Monitor::query()->create([
            'name' => 'Test Monitor',
            'type' => Type::Ping,
            'settings' => [
                'host' => '127.0.0.1',
                'port' => 53
            ],
            'interval' => '* * * * *',
            'retries' => 1,
            'timeout' => 1,
        ]);

        $pingMock = $this->partialMock(Ping::class, function (MockInterface $mock) {
            $mock->shouldReceive('ping')->andReturn(10);
        });

        $this->app->bind(Ping::class, fn () => $pingMock);

        Http::fake([
            'http://service' => Http::response()
        ]);

        $this->artisan(CheckUptimeCommand::class, [
            'monitorId' => $monitor->id,
        ]);
        /** @var Result $result */
        $result = $monitor->results->first();

        $this->assertEquals(10, $result->total_time);
    }
}
