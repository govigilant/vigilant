<?php

namespace Vigilant\Uptime\Tests\Feature;

use Vigilant\Uptime\Commands\AggregateResultsCommand;
use Vigilant\Uptime\Enums\Type;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Tests\TestCase;

class AggregateResultsTest extends TestCase
{
    public function test_it_aggregates_uptime_results(): void
    {
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

        $this->assertNotNull($monitor);

        for ($minute = 0; $minute < (60 * 24); $minute++) {

            $date = now()->subMinutes($minute);

            $monitor->results()->create([
                'total_time' => $minute,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

        }

        $this->artisan(AggregateResultsCommand::class);

        $this->assertCount(23, $monitor->aggregatedResults);
    }
}
