<?php

namespace Vigilant\Healthchecks\Actions;

use Illuminate\Support\Collection;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Models\Metric;
use Vigilant\Healthchecks\Notifications\Conditions\MetricIncreaseTimeframeCondition;
use Vigilant\Healthchecks\Notifications\DiskUsageNotification;
use Vigilant\Healthchecks\Notifications\MetricIncreasingNotification;
use Vigilant\Healthchecks\Notifications\MetricNotification;
use Vigilant\Healthchecks\Notifications\MetricSpikeNotification;

class CheckMetric
{
    public function check(Healthcheck $healthcheck, int $runId): void
    {
        /** @var Collection<int, Metric> $metrics */
        $metrics = $healthcheck->metrics()
            ->where('run_id', '=', $runId)
            ->get();

        if ($metrics->isEmpty()) {
            return;
        }

        foreach ($metrics as $metric) {
            MetricNotification::notify($metric);
        }

        $this->checkIncreasingMetrics($healthcheck, $metrics);
        $this->checkDiskUsage($healthcheck, $metrics);
    }

    protected function checkIncreasingMetrics(Healthcheck $healthcheck, Collection $metrics): void
    {
        $metricsByKey = $metrics->groupBy('key');

        foreach ($metricsByKey as $key => $keyMetrics) {
            $currentMetric = $keyMetrics->first();
            $increaseData = $this->calculateMetricIncrease($healthcheck, $key, $currentMetric);

            if ($increaseData !== null) {
                if (isset($increaseData['detection_type']) && $increaseData['detection_type'] === 'sudden_spike') {
                    MetricSpikeNotification::notify($currentMetric, $increaseData);
                } else {
                    MetricIncreasingNotification::notify($currentMetric, $increaseData);
                }
            }
        }
    }

    public function calculateMetricIncrease(Healthcheck $healthcheck, string $key, Metric $currentMetric): ?array
    {
        /** @var Collection<int, Metric> $historicalMetrics */
        $historicalMetrics = $healthcheck->metrics()
            ->where('key', '=', $key)
            ->where('created_at', '<=', $currentMetric->created_at)
            ->orderBy('created_at', 'desc')
            ->limit(60)
            ->get();

        if ($historicalMetrics->count() < 2 || $currentMetric->created_at === null) {
            return null;
        }

        $currentValue = $currentMetric->value;

        // Spike detection
        $recentMetrics = $historicalMetrics->take(5);
        if ($recentMetrics->count() >= 3) {
            $spikeResult = $this->detectSpike($recentMetrics, $currentValue, $currentMetric);
            if ($spikeResult !== null) {
                return $spikeResult;
            }
        }

        $intervalResults = [];
        $intervals = MetricIncreaseTimeframeCondition::INTERVALS;

        foreach ($intervals as $index => $interval) {
            $minBound = $interval;
            $maxBound = $intervals[$index + 1] ?? null;

            $baselineMetric = $this->findBaselineMetricForRange(
                $historicalMetrics,
                $currentMetric,
                $minBound,
                $maxBound
            );

            if ($baselineMetric === null) {
                continue;
            }

            $oldValue = $baselineMetric->value;

            if ($oldValue == 0) {
                continue;
            }

            $percentIncrease = (($currentValue - $oldValue) / $oldValue) * 100;

            if ($percentIncrease <= 0) {
                continue;
            }

            $intervalResults[] = [
                'key' => $key,
                'old_value' => $oldValue,
                'new_value' => $currentValue,
                'unit' => $currentMetric->unit,
                'percent_increase' => $percentIncrease,
                'timeframe_minutes' => $interval,
                'sample_size' => $historicalMetrics->count(),
                'detection_type' => 'long_term_trend',
            ];
        }

        if ($intervalResults === []) {
            return null;
        }

        return $intervalResults;
    }

    protected function findBaselineMetricForRange(
        Collection $historicalMetrics,
        Metric $currentMetric,
        int $minBoundary,
        ?int $maxBoundary
    ): ?Metric {
        if ($currentMetric->created_at === null) {
            return null;
        }

        return $historicalMetrics->first(function (Metric $metric) use ($currentMetric, $minBoundary, $maxBoundary) {
            if ($metric->is($currentMetric) || $metric->created_at === null) {
                return false;
            }

            $difference = $currentMetric->created_at->diffInMinutes($metric->created_at, true);

            if ($difference < $minBoundary) {
                return false;
            }

            if ($maxBoundary !== null && $difference >= $maxBoundary) {
                return false;
            }

            return true;
        });
    }

    public function detectSpike(Collection $recentMetrics, float $currentValue, Metric $currentMetric): ?array
    {
        /** @var Metric $recentOldest */
        $recentOldest = $recentMetrics->last();
        $recentOldestValue = $recentOldest->value;

        if ($recentOldestValue == 0) {
            return null;
        }

        $percentIncrease = (($currentValue - $recentOldestValue) / $recentOldestValue) * 100;

        // Spike threshold: 50% increase
        if ($percentIncrease < 50) {
            return null;
        }

        $timeframeMinutes = $currentMetric->created_at?->diffInMinutes($recentOldest->created_at, true) ?? 0;

        return [
            'key' => $currentMetric->key,
            'old_value' => $recentOldestValue,
            'new_value' => $currentValue,
            'unit' => $currentMetric->unit,
            'percent_increase' => $percentIncrease,
            'timeframe_minutes' => $timeframeMinutes,
            'sample_size' => $recentMetrics->count(),
            'detection_type' => 'sudden_spike',
        ];
    }

    protected function checkDiskUsage(Healthcheck $healthcheck, Collection $metrics): void
    {
        /** @var ?Metric $diskMetric */
        $diskMetric = $metrics->firstWhere('key', 'disk_usage');

        if (! $diskMetric || $diskMetric->unit !== '%') {
            return;
        }

        /** @var Collection<int, Metric> $historicalMetrics */
        $historicalMetrics = $healthcheck->metrics()
            ->where('key', 'disk_usage')
            ->where('created_at', '<=', $diskMetric->created_at)
            ->orderBy('created_at', 'desc')
            ->limit(60)
            ->get();

        $currentUsage = $diskMetric->value;
        /** @var ?Metric $oldestMetric */
        $oldestMetric = $historicalMetrics->last();

        if ($oldestMetric === null) {
            return;
        }

        $oldestUsage = $oldestMetric->value;

        $timeframeHours = $oldestMetric->created_at?->diffInHours($diskMetric->created_at, true) ?? 0;

        if ($timeframeHours == 0) {
            return;
        }

        $velocity = ($currentUsage - $oldestUsage) / $timeframeHours;

        if ($velocity <= 0) {
            return;
        }

        $remainingSpace = 100 - $currentUsage;
        $hoursUntilFull = $remainingSpace / $velocity;

        if ($hoursUntilFull < 0) {
            return;
        }

        $estimatedFullAt = now()->addHours($hoursUntilFull);

        DiskUsageNotification::notify(
            $healthcheck,
            $currentUsage,
            $velocity,
            $hoursUntilFull,
            $estimatedFullAt
        );
    }
}
