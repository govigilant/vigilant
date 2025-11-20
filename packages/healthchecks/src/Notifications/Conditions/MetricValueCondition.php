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

    public function operators(): array
    {
        return [
            '<' => 'Less than',
            '<=' => 'Less or equal than',
            '>' => 'Greater than',
            '>=' => 'Greater or equal than',
        ];
    }

    public function applies(Notification $notification, ?string $operand, ?string $operator, mixed $value, ?array $meta): bool
    {
        /** @var MetricNotification $notification */
        $metric = $notification->metric->value;

        return match ($operator) {
            '>' => $metric > $value,
            '>=' => $metric >= $value,
            '<' => $metric < $value,
            '<=' => $metric <= $value,
            default => false,
        };
    }
}
