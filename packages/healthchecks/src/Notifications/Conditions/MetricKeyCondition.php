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

    public function operators(): array
    {
        return [
            '=' => 'is',
            '!=' => 'is not',
        ];
    }

    public function applies(Notification $notification, ?string $operand, ?string $operator, mixed $value, ?array $meta): bool
    {
        /** @var MetricNotification $notification */

        $key = $notification->metric->key;

        return match ($operator) {
            '=' => $key === $value,
            '!=' => $key !== $value,
            default => $key === $value,
        };
    }
}
