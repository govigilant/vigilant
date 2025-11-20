<?php

namespace Vigilant\Healthchecks\Tests\Actions;

use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Healthchecks\Actions\CheckMetric;
use Vigilant\Healthchecks\Enums\Type;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Models\Metric;
use Vigilant\Healthchecks\Notifications\DiskUsageNotification;
use Vigilant\Healthchecks\Notifications\MetricIncreasingNotification;
use Vigilant\Healthchecks\Notifications\MetricNotification;
use Vigilant\Healthchecks\Notifications\MetricSpikeNotification;
use Vigilant\Healthchecks\Tests\TestCase;

class CheckMetricTest extends TestCase
{
    #[Test]
    public function it_does_nothing_when_no_metrics_exist(): void
    {
        MetricIncreasingNotification::fake();
        MetricSpikeNotification::fake();

        $healthcheck = Healthcheck::query()->create([
            'domain' => 'example.com',
            'type' => Type::Laravel,
            'interval' => 5,
            'token' => 'test-token',
        ]);

        /** @var CheckMetric $action */
        $action = app(CheckMetric::class);
        $action->check($healthcheck, 1);

        $this->assertFalse(MetricIncreasingNotification::wasDispatched());
        $this->assertFalse(MetricSpikeNotification::wasDispatched());
    }

    #[Test]
    public function it_detects_metric_spike(): void
    {
        MetricSpikeNotification::fake();

        $healthcheck = Healthcheck::query()->create([
            'domain' => 'example.com',
            'type' => Type::Laravel,
            'interval' => 5,
            'token' => 'test-token',
        ]);

        $now = Carbon::now();

        // Create four stable metrics and a sudden spike on the fifth run
        foreach ([100, 105, 110, 115] as $index => $value) {
            Metric::query()->create([
                'healthcheck_id' => $healthcheck->id,
                'run_id' => $index + 1,
                'key' => 'memory_usage',
                'value' => $value,
                'unit' => 'MB',
                'created_at' => $now->copy()->subMinutes(4 - $index),
            ]);
        }

        Metric::query()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 5,
            'key' => 'memory_usage',
            'value' => 200,
            'unit' => 'MB',
            'created_at' => $now,
        ]);

        /** @var CheckMetric $action */
        $action = app(CheckMetric::class);
        $action->check($healthcheck, 5);

        $this->assertTrue(MetricSpikeNotification::wasDispatched(function ($notification): bool {
            if (! $notification instanceof MetricSpikeNotification) {
                return true;
            }

            return $notification->spikeMetrics['key'] === 'memory_usage' &&
                $notification->spikeMetrics['old_value'] == 100 &&
                $notification->spikeMetrics['new_value'] == 200 &&
                $notification->spikeMetrics['percent_increase'] == 100 &&
                $notification->spikeMetrics['sample_size'] == 5 &&
                $notification->spikeMetrics['detection_type'] === 'sudden_spike';
        }));
    }

    #[Test]
    public function it_detects_long_term_metric_increase(): void
    {
        MetricIncreasingNotification::fake();

        $healthcheck = Healthcheck::query()->create([
            'domain' => 'example.com',
            'type' => Type::Laravel,
            'interval' => 5,
            'token' => 'test-token',
        ]);

        $now = Carbon::now();

        // Create 10 historical metrics showing gradual increase from 100 to 190
        for ($i = 0; $i < 10; $i++) {
            Metric::query()->create([
                'healthcheck_id' => $healthcheck->id,
                'run_id' => $i + 1,
                'key' => 'memory_usage',
                'value' => 100 + ($i * 10),
                'unit' => 'MB',
                'created_at' => $now->copy()->subMinutes(90 - ($i * 10)),
            ]);
        }

        /** @var CheckMetric $action */
        $action = app(CheckMetric::class);
        $action->check($healthcheck, 10);

        $this->assertTrue(MetricIncreasingNotification::wasDispatched(function ($notification): bool {
            if ($notification instanceof MetricNotification || $notification instanceof MetricSpikeNotification) {
                return true;
            }

            return $notification->increasedMetrics['key'] === 'memory_usage' &&
                $notification->increasedMetrics['old_value'] == 100 &&
                $notification->increasedMetrics['new_value'] == 190 &&
                $notification->increasedMetrics['percent_increase'] == 90;
        }));
    }

    #[Test]
    public function it_notifies_when_disk_usage_is_increasing(): void
    {
        DiskUsageNotification::fake();

        $healthcheck = Healthcheck::query()->create([
            'domain' => 'example.com',
            'type' => Type::Laravel,
            'interval' => 5,
            'token' => 'disk-test-token',
        ]);

        $now = Carbon::now();

        Metric::query()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 1,
            'key' => 'disk_usage',
            'value' => 70,
            'unit' => '%',
            'created_at' => $now->copy()->subHours(5),
        ]);

        Metric::query()->create([
            'healthcheck_id' => $healthcheck->id,
            'run_id' => 2,
            'key' => 'disk_usage',
            'value' => 90,
            'unit' => '%',
            'created_at' => $now,
        ]);

        /** @var CheckMetric $action */
        $action = app(CheckMetric::class);
        $action->check($healthcheck, 2);

        $this->assertTrue(DiskUsageNotification::wasDispatched(function ($notification): bool {
            if (! $notification instanceof DiskUsageNotification) {
                return true;
            }

            return $notification->runId === 2 &&
                $notification->currentUsage === 90.0 &&
                $notification->velocity === 4.0 &&
                $notification->hoursUntilFull === 2.5;
        }));
    }
}
