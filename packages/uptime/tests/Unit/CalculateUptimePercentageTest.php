<?php

namespace Vigilant\Uptime\Tests\Unit;

use Illuminate\Support\Carbon;
use Vigilant\Uptime\Actions\CalculateUptimePercentage;
use Vigilant\Uptime\Models\Downtime;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\ResultAggregate;
use Vigilant\Uptime\Tests\TestCase;

class CalculateUptimePercentageTest extends TestCase
{
    public function test_it_calculates_uptime(): void
    {
        Carbon::setTestNow(Carbon::parse('2024-02-24 10:00:00'));

        /** @var Monitor $monitor */
        $monitor = Monitor::withoutEvents(fn (): Monitor => Monitor::factory()->create());

        ResultAggregate::query()->create([
            'monitor_id' => $monitor->id,
            'total_time' => 0,
            'created_at' => '2024-02-24 00:00:00',
        ]);

        Downtime::query()->create([
            'monitor_id' => $monitor->id,
            'start' => '2024-02-24 00:00:00',
            'end' => '2024-02-24 01:00:00',
            'created_at' => '2024-02-24 00:00:00',
            'updated_at' => '2024-02-24 01:00:00',
        ]);

        /** @var CalculateUptimePercentage $action */
        $action = app(CalculateUptimePercentage::class);

        $this->assertEquals(90, $action->calculate($monitor));

    }
}
