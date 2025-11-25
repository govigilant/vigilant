<?php

namespace Vigilant\Healthchecks\Notifications;

use Vigilant\Healthchecks\Models\Metric;
use Vigilant\Healthchecks\Notifications\Conditions\MetricIncreasePercentCondition;
use Vigilant\Healthchecks\Notifications\Conditions\MetricIncreaseTimeframeCondition;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class MetricIncreasingNotification extends Notification implements HasSite
{
    public static string $name = 'Metric increasing';

    public Level $level = Level::Warning;

    public static ?int $defaultCooldown = 60;

    public static array $defaultConditions = [
        'type' => 'group',
        'operator' => 'all',
        'children' => [
            [
                'type' => 'condition',
                'condition' => MetricIncreasePercentCondition::class,
                'operator' => '>=',
                'value' => 50,
            ],
            [
                'type' => 'condition',
                'condition' => MetricIncreaseTimeframeCondition::class,
                'operator' => '=',
                'value' => 5,
            ],
        ],
    ];

    public function __construct(
        public Metric $metric,
        public array $increasedMetrics = []
    ) {}

    public function title(): string
    {
        $domain = $this->metric->healthcheck->domain ?? '?';

        return __('Metric increasing for :domain', ['domain' => $domain]);
    }

    public function description(): string
    {
        $key = $this->metric->key;
        $value = $this->metric->value;
        $unit = $this->metric->unit;

        if (! empty($this->increasedMetrics)) {
            $metricData = $this->increasedMetrics;

            if (isset($metricData[0]) && is_array($metricData[0])) {
                $sorted = $metricData;
                usort($sorted, static function (array $left, array $right): int {
                    return ($left['timeframe_minutes'] ?? PHP_INT_MAX) <=> ($right['timeframe_minutes'] ?? PHP_INT_MAX);
                });
                $metricData = $sorted[0] ?? [];
            }

            if (is_array($metricData) && ! empty($metricData)) {
                $percentIncrease = round($metricData['percent_increase'] ?? 0, 1);
                $timeframeMinutes = $metricData['timeframe_minutes'] ?? 0;
                $oldValue = $metricData['old_value'] ?? 0;

                return __('The metric ":key" has increased by :percent% (from :old_value:unit to :new_value:unit) over the past :timeframe minutes.', [
                    'key' => $key,
                    'percent' => $percentIncrease,
                    'old_value' => $oldValue,
                    'new_value' => $value,
                    'unit' => $unit,
                    'timeframe' => $timeframeMinutes,
                ]);
            }
        }

        return __('The metric ":key" has increased to :value:unit.', [
            'key' => $key,
            'value' => $value,
            'unit' => $unit,
        ]);
    }

    public static function info(): ?string
    {
        return __('Triggered when a metric increases by a specified percentage within a timeframe.');
    }

    public function uniqueId(): string
    {
        return 'metric-increasing-'.$this->metric->key;
    }

    public function site(): ?Site
    {
        return $this->metric->healthcheck?->site;
    }
}
