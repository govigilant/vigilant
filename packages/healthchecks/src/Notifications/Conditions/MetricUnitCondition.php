<?php

namespace Vigilant\Healthchecks\Notifications\Conditions;

use Vigilant\Healthchecks\Models\Metric;
use Vigilant\Healthchecks\Notifications\MetricNotification;
use Vigilant\Notifications\Conditions\SelectCondition;
use Vigilant\Notifications\Notifications\Notification;

class MetricUnitCondition extends SelectCondition
{
    public static string $name = 'Metric unit';

    public function options(): array
    {
        return Metric::query()
            ->whereNotNull('unit')
            ->distinct('unit')
            ->orderBy('unit')
            ->pluck('unit', 'unit')
            ->toArray();
    }

    public function applies(Notification $notification, ?string $operand, ?string $operator, mixed $value, ?array $meta): bool
    {
        /** @var MetricNotification $notification */
        /** @var \Illuminate\Database\Eloquent\Collection<int, \Vigilant\Healthchecks\Models\Metric> $metrics */
        $metrics = $notification->healthcheck->metrics()
            ->where('run_id', $notification->runId)
            ->get();

        foreach ($metrics as $metric) {
            if ($metric->unit === $value) {
                return true;
            }
        }

        return false;
    }
}
