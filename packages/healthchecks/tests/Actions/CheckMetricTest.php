<?php

namespace Vigilant\Healthchecks\Tests\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Vigilant\Healthchecks\Actions\CheckMetric;
use Vigilant\Healthchecks\Enums\Type;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Models\Metric;
use Vigilant\Healthchecks\Notifications\Conditions\MetricIncreaseTimeframeCondition;
use Vigilant\Healthchecks\Notifications\DiskUsageNotification;
use Vigilant\Healthchecks\Notifications\MetricIncreasingNotification;
use Vigilant\Healthchecks\Notifications\MetricSpikeNotification;
use Vigilant\Healthchecks\Tests\TestCase;

class CheckMetricTest extends TestCase
{
    #[Test]
    public function it_does_nothing_when_no_metrics_exist(): void
    {
        MetricIncreasingNotification::fake();
        MetricSpikeNotification::fake();

        Bus::fake();

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

        // Create 7 historical metrics covering the last 60 minutes
        for ($i = 0; $i < 7; $i++) {
            Metric::query()->create([
                'healthcheck_id' => $healthcheck->id,
                'run_id' => $i + 1,
                'key' => 'memory_usage',
                'value' => 100 + ($i * 15),
                'unit' => 'MB',
                'created_at' => $now->copy()->subMinutes(60 - ($i * 10)),
            ]);
        }

        /** @var CheckMetric $action */
        $action = app(CheckMetric::class);
        $action->check($healthcheck, 7);

        $matched = false;

        MetricIncreasingNotification::wasDispatched(function ($notification) use (&$matched): bool {
            if (! $notification instanceof MetricIncreasingNotification) {
                return true;
            }

            $entries = $notification->increasedMetrics;

            if (! isset($entries[0]) || ! is_array($entries[0])) {
                return true;
            }

            $match = collect($entries)->first(function (array $entry): bool {
                return ($entry['key'] ?? null) === 'memory_usage'
                    && ($entry['old_value'] ?? null) == 100
                    && ($entry['new_value'] ?? null) == 190
                    && round($entry['percent_increase'] ?? 0, 0) == 90
                    && ($entry['timeframe_minutes'] ?? null) === 60;
            });

            if ($match !== null) {
                $matched = true;
            }

            return true;
        });

        $this->assertTrue($matched);
    }

    #[Test]
    public function it_only_checks_configured_timeframes(): void
    {
        MetricIncreasingNotification::fake();
        MetricSpikeNotification::fake();

        $healthcheck = Healthcheck::query()->create([
            'domain' => 'example.com',
            'type' => Type::Laravel,
            'interval' => 5,
            'token' => 'interval-test-token',
        ]);

        $now = Carbon::now();
        $runId = 0;

        $dataPoints = [
            60 => 10,
            30 => 15,
            15 => 18,
            10 => 20,
            5 => 22,
            2 => 23,
            0 => 24,
        ];

        foreach ($dataPoints as $minutesAgo => $value) {
            $runId++;

            Metric::query()->create([
                'healthcheck_id' => $healthcheck->id,
                'run_id' => $runId,
                'key' => 'cpu_load',
                'value' => $value,
                'unit' => '%',
                'created_at' => $now->copy()->subMinutes($minutesAgo),
            ]);
        }

        /** @var CheckMetric $action */
        $action = app(CheckMetric::class);
        $action->check($healthcheck, $runId);

        $matched = false;

        MetricIncreasingNotification::wasDispatched(function ($notification) use (&$matched): bool {
            if (! $notification instanceof MetricIncreasingNotification) {
                return true;
            }

            $entries = array_filter(
                $notification->increasedMetrics,
                static fn ($entry) => is_array($entry)
            );

            if ($entries === []) {
                return true;
            }

            $timeframes = array_map(
                static fn (array $entry) => $entry['timeframe_minutes'] ?? null,
                $entries
            );

            $timeframes = array_filter($timeframes, static fn ($value) => $value !== null);
            sort($timeframes);

            if ($timeframes === MetricIncreaseTimeframeCondition::INTERVALS) {
                $matched = true;
            }

            return true;
        });

        $this->assertTrue($matched);
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

        $this->assertTrue(DiskUsageNotification::wasDispatched(function ($notification) use ($healthcheck): bool {
            if (! $notification instanceof DiskUsageNotification) {
                return true;
            }

            return $notification->healthcheck->is($healthcheck) &&
                $notification->currentUsage === 90.0 &&
                $notification->velocity === 4.0 &&
                $notification->hoursUntilFull === 2.5;
        }));
    }
}
