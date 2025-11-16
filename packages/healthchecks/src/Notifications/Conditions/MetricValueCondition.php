<?php

namespace Vigilant\Healthchecks\Notifications\Conditions;

use Vigilant\Healthchecks\Notifications\MetricNotification;
use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Enums\ConditionType;
use Vigilant\Notifications\Notifications\Notification;

class MetricValueCondition extends Condition
{
    public static string $name = 'Metric value';

    public ConditionType $type = ConditionType::Number;

    public function metadata(): array
    {
        return [];
    }

    public function applies(Notification $notification, ?string $operand, ?string $operator, mixed $value, ?array $meta): bool
    {
        /** @var MetricNotification $notification */
        /** @var \Illuminate\Database\Eloquent\Collection<int, \Vigilant\Healthchecks\Models\Metric> $metrics */
        $metrics = $notification->healthcheck->metrics()
            ->where('run_id', $notification->runId)
            ->get();

        if ($operand !== null) {
            $metrics = $metrics->where('key', $operand);
        }

        foreach ($metrics as $metric) {
            $result = match ($operator) {
                '>' => $metric->value > $value,
                '>=' => $metric->value >= $value,
                '<' => $metric->value < $value,
                '<=' => $metric->value <= $value,
                '=' => $metric->value == $value,
                '!=' => $metric->value != $value,
                default => false,
            };

            if ($result) {
                return true;
            }
        }

        return false;
    }
}
