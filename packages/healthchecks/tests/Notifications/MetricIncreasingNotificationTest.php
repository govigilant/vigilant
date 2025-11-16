<?php

namespace Vigilant\Healthchecks\Tests\Notifications;

use PHPUnit\Framework\Attributes\Test;
use Vigilant\Healthchecks\Actions\CheckMetric;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Models\Metric;
use Vigilant\Healthchecks\Notifications\MetricIncreasingNotification;
use Vigilant\Healthchecks\Tests\TestCase;

class MetricIncreasingNotificationTest extends TestCase
{
    #[Test]
    public function it_sends_notification_when_metric_increases_significantly(): void
    {
        MetricIncreasingNotification::fake();

        $healthcheck = Healthcheck::factory()->create();
        $baseTime = now();

        // Create 5 metrics showing increase from 20% to 80% over 5 minutes
        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 1,
            'key' => 'cpu_usage',
            'value' => 20,
            'unit' => '%',
            'created_at' => $baseTime->copy()->subMinutes(4),
        ]);

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 2,
            'key' => 'cpu_usage',
            'value' => 35,
            'unit' => '%',
            'created_at' => $baseTime->copy()->subMinutes(3),
        ]);

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 3,
            'key' => 'cpu_usage',
            'value' => 50,
            'unit' => '%',
            'created_at' => $baseTime->copy()->subMinutes(2),
        ]);

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 4,
            'key' => 'cpu_usage',
            'value' => 65,
            'unit' => '%',
            'created_at' => $baseTime->copy()->subMinutes(1),
        ]);

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 5,
            'key' => 'cpu_usage',
            'value' => 80,
            'unit' => '%',
            'created_at' => $baseTime,
        ]);

        $action = app(CheckMetric::class);
        $action->check($healthcheck, 5);

        $this->assertTrue(MetricIncreasingNotification::wasDispatched());
    }

    #[Test]
    public function it_calculates_percentage_increase_correctly(): void
    {
        $healthcheck = Healthcheck::factory()->create();
        $baseTime = now();

        // 20 to 80 is 300% increase (60 point increase on 20 base = 300%)
        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 1,
            'key' => 'cpu_usage',
            'value' => 20,
            'unit' => '%',
            'created_at' => $baseTime->copy()->subMinutes(5),
        ]);

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 2,
            'key' => 'cpu_usage',
            'value' => 80,
            'unit' => '%',
            'created_at' => $baseTime,
        ]);

        $increasedMetrics = [
            [
                'key' => 'cpu_usage',
                'old_value' => 20,
                'new_value' => 80,
                'unit' => '%',
                'percent_increase' => 300,
                'timeframe_minutes' => 5,
                'sample_size' => 2,
            ],
        ];

        $notification = new MetricIncreasingNotification($healthcheck, 2, $increasedMetrics);

        $this->assertEquals(300, $notification->increasedMetrics[0]['percent_increase']);
        $this->assertEquals(5, $notification->increasedMetrics[0]['timeframe_minutes']);
    }

    #[Test]
    public function it_does_not_send_notification_when_metric_decreases(): void
    {
        MetricIncreasingNotification::fake();

        $healthcheck = Healthcheck::factory()->create();
        $baseTime = now();

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 1,
            'key' => 'cpu_usage',
            'value' => 80,
            'unit' => '%',
            'created_at' => $baseTime->copy()->subMinutes(5),
        ]);

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 2,
            'key' => 'cpu_usage',
            'value' => 20,
            'unit' => '%',
            'created_at' => $baseTime,
        ]);

        $action = app(CheckMetric::class);
        $action->check($healthcheck, 2);

        $this->assertFalse(MetricIncreasingNotification::wasDispatched());
    }

    #[Test]
    public function it_does_not_send_notification_when_insufficient_data(): void
    {
        MetricIncreasingNotification::fake();

        $healthcheck = Healthcheck::factory()->create();

        Metric::factory()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 1,
            'key' => 'cpu_usage',
            'value' => 80,
            'unit' => '%',
            'created_at' => now(),
        ]);

        $action = app(CheckMetric::class);
        $action->check($healthcheck, 1);

        $this->assertFalse(MetricIncreasingNotification::wasDispatched());
    }
}
