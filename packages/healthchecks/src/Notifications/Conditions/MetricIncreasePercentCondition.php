<?php

namespace Vigilant\Healthchecks\Notifications\Conditions;

use Vigilant\Healthchecks\Notifications\MetricIncreasingNotification;
use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Enums\ConditionType;
use Vigilant\Notifications\Notifications\Notification;

class MetricIncreasePercentCondition extends Condition
{
    public static string $name = 'Increase percentage';

    public ConditionType $type = ConditionType::Number;

    public function metadata(): array
    {
        return [];
    }

    public function applies(Notification $notification, ?string $operand, ?string $operator, mixed $value, ?array $meta): bool
    {
        /** @var MetricIncreasingNotification $notification */
        if (empty($notification->increasedMetrics)) {
            return false;
        }

        foreach ($notification->increasedMetrics as $metricData) {
            if ($operand !== null && $metricData['key'] !== $operand) {
                continue;
            }

            $percentIncrease = $metricData['percent_increase'];

            $result = match ($operator) {
                '>' => $percentIncrease > $value,
                '>=' => $percentIncrease >= $value,
                '<' => $percentIncrease < $value,
                '<=' => $percentIncrease <= $value,
                '=' => $percentIncrease == $value,
                '!=' => $percentIncrease != $value,
                default => false,
            };

            if ($result) {
                return true;
            }
        }

        return false;
    }
}
