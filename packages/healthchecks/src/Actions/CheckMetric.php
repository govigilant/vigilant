<?php

namespace Vigilant\Healthchecks\Actions;

use Illuminate\Support\Collection;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Models\Metric;
use Vigilant\Healthchecks\Notifications\DiskUsageNotification;
use Vigilant\Healthchecks\Notifications\MetricIncreasingNotification;
use Vigilant\Healthchecks\Notifications\MetricNotification;

class CheckMetric
{
    public function check(Healthcheck $healthcheck, int $runId): void
    {
        /** @var Collection<int, Metric> $metrics */
        $metrics = $healthcheck->metrics()
            ->where('run_id', $runId)
            ->get();

        if ($metrics->isEmpty()) {
            return;
        }

        MetricNotification::notify($healthcheck, $runId);

        $this->checkIncreasingMetrics($healthcheck, $runId, $metrics);
        $this->checkDiskUsage($healthcheck, $runId, $metrics);
    }

    protected function checkIncreasingMetrics(Healthcheck $healthcheck, int $runId, Collection $metrics): void
    {
        $increasedMetrics = [];
        $metricsByKey = $metrics->groupBy('key');

        foreach ($metricsByKey as $key => $keyMetrics) {
            $currentMetric = $keyMetrics->first();
            $increaseData = $this->calculateMetricIncrease($healthcheck, $key, $currentMetric);

            if ($increaseData !== null) {
                $increasedMetrics[] = $increaseData;
            }
        }

        if (! empty($increasedMetrics)) {
            MetricIncreasingNotification::notify($healthcheck, $runId, $increasedMetrics);
        }
    }

    protected function calculateMetricIncrease(Healthcheck $healthcheck, string $key, Metric $currentMetric): ?array
    {
        $historicalMetrics = $healthcheck->metrics()
            ->where('key', '=', $key)
            ->where('created_at', '<=', $currentMetric->created_at)
            ->orderBy('created_at', 'desc')
            ->limit(60)
            ->get();

        if ($historicalMetrics->count() < 2) {
            return null;
        }

        $currentValue = $currentMetric->value;
        $oldestMetric = $historicalMetrics->last();
        $oldestValue = $oldestMetric->value;

        if ($oldestValue == 0) {
            return null;
        }

        $percentIncrease = (($currentValue - $oldestValue) / $oldestValue) * 100;

        if ($percentIncrease <= 0) {
            return null;
        }

        $timeframeMinutes = $currentMetric->created_at->diffInMinutes($oldestMetric->created_at);

        return [
            'key' => $key,
            'old_value' => $oldestValue,
            'new_value' => $currentValue,
            'unit' => $currentMetric->unit,
            'percent_increase' => $percentIncrease,
            'timeframe_minutes' => $timeframeMinutes,
            'sample_size' => $historicalMetrics->count(),
        ];
    }

    protected function checkDiskUsage(Healthcheck $healthcheck, int $runId, $metrics): void
    {
        $diskMetric = $metrics->firstWhere('key', 'disk_usage');

        if (! $diskMetric || $diskMetric->unit !== '%') {
            return;
        }

        $historicalMetrics = $healthcheck->metrics()
            ->where('key', 'disk_usage')
            ->where('created_at', '<=', $diskMetric->created_at)
            ->orderBy('created_at', 'desc')
            ->limit(60)
            ->get();

        if ($historicalMetrics->count() < 2) {
            return;
        }

        $currentUsage = $diskMetric->value;
        $oldestMetric = $historicalMetrics->last();
        $oldestUsage = $oldestMetric->value;

        $timeframeHours = $diskMetric->created_at->diffInHours($oldestMetric->created_at);

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

        $estimatedFullAt = now()->addHours($hoursUntilFull)->toDateTimeString();

        DiskUsageNotification::notify(
            $healthcheck,
            $runId,
            $currentUsage,
            $velocity,
            $hoursUntilFull,
            $estimatedFullAt
        );
    }
}
