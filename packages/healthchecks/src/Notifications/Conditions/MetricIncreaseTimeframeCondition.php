<?php

namespace Vigilant\Healthchecks\Notifications\Conditions;

use Vigilant\Healthchecks\Notifications\MetricIncreasingNotification;
use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Enums\ConditionType;
use Vigilant\Notifications\Notifications\Notification;

class MetricIncreaseTimeframeCondition extends Condition
{
    public static string $name = 'Timeframe (minutes)';

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
        /** @var MetricIncreasingNotification $notification */
        $metricDatas = $notification->increasedMetrics;

        if (empty($metricDatas)) {
            return false;
        }

        if (! isset($metricDatas[0]) || ! is_array($metricDatas[0])) {
            $metricDatas = [$metricDatas];
        }

        foreach ($metricDatas as $metricData) {
            if (! is_array($metricData)) {
                continue;
            }

            if ($operand !== null && ($metricData['key'] ?? null) !== $operand) {
                continue;
            }

            $timeframeMinutes = $metricData['timeframe_minutes'] ?? null;

            if ($timeframeMinutes === null) {
                continue;
            }

            $result = match ($operator) {
                '>' => $timeframeMinutes > $value,
                '>=' => $timeframeMinutes >= $value,
                '<' => $timeframeMinutes < $value,
                '<=' => $timeframeMinutes <= $value,
                default => false,
            };

            if ($result) {
                return true;
            }
        }

        return false;
    }
}
