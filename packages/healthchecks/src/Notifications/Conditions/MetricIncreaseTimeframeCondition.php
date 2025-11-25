<?php

namespace Vigilant\Healthchecks\Notifications\Conditions;

use Vigilant\Healthchecks\Notifications\MetricIncreasingNotification;
use Vigilant\Notifications\Conditions\SelectCondition;
use Vigilant\Notifications\Enums\ConditionType;
use Vigilant\Notifications\Notifications\Notification;

class MetricIncreaseTimeframeCondition extends SelectCondition
{
    public const INTERVALS = [2, 5, 10, 15, 30, 60];

    public static string $name = 'Timeframe (minutes)';

    public ConditionType $type = ConditionType::Select;

    /** @return array<int, string> */
    public function options(): array
    {
        $options = [];

        foreach (self::INTERVALS as $minutes) {
            $options[$minutes] = sprintf('%d minutes', $minutes);
        }

        return $options;
    }

    public function applies(Notification $notification, ?string $operand, ?string $operator, mixed $value, ?array $meta): bool
    {
        /** @var MetricIncreasingNotification $notification */
        $metricDatas = $notification->increasedMetrics;

        if (empty($metricDatas) || $value === null) {
            return false;
        }

        if (! isset($metricDatas[0]) || ! is_array($metricDatas[0])) {
            $metricDatas = [$metricDatas];
        }

        $threshold = (int) $value;

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

            if ((int) $timeframeMinutes === $threshold) {
                return true;
            }
        }

        return false;
    }
}
