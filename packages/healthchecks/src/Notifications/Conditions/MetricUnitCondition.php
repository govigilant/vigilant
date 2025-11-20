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
        return $notification->metric->unit === $value;
    }
}
