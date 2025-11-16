<?php

namespace Vigilant\Healthchecks\Notifications\Conditions;

use Vigilant\Healthchecks\Models\Metric;
use Vigilant\Healthchecks\Notifications\MetricNotification;
use Vigilant\Notifications\Conditions\SelectCondition;
use Vigilant\Notifications\Notifications\Notification;

class MetricKeyCondition extends SelectCondition
{
    public static string $name = 'Metric key';

    public function options(): array
    {
        return Metric::query()
            ->whereNotNull('key')
            ->distinct('key')
            ->orderBy('key')
            ->pluck('key', 'key')
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
            if ($metric->key === $value) {
                return true;
            }
        }

        return false;
    }
}
