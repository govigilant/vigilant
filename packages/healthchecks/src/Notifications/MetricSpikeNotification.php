<?php

namespace Vigilant\Healthchecks\Notifications;

use Vigilant\Healthchecks\Models\Metric;
use Vigilant\Healthchecks\Notifications\Conditions\MetricIncreasePercentCondition;
use Vigilant\Healthchecks\Notifications\Conditions\MetricIncreaseTimeframeCondition;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class MetricSpikeNotification extends Notification implements HasSite
{
    public static string $name = 'Metric spike detected';

    public Level $level = Level::Critical;

    public static ?int $defaultCooldown = 15;

    public static array $defaultConditions = [
        'type' => 'group',
        'operator' => 'all',
        'children' => [
            [
                'type' => 'condition',
                'condition' => MetricIncreasePercentCondition::class,
                'operator' => '>=',
                'value' => 40,
            ],
            [
                'type' => 'condition',
                'condition' => MetricIncreaseTimeframeCondition::class,
                'operator' => '<=',
                'value' => 5,
            ],
        ],
    ];

    public function __construct(
        public Metric $metric,
        public array $spikeMetrics = []
    ) {}

    public function title(): string
    {
        $domain = $this->metric->healthcheck->domain ?? '?';

        return __('Metric spike detected for :domain', ['domain' => $domain]);
    }

    public function description(): string
    {
        $key = $this->metric->key;
        $value = $this->metric->value;
        $unit = $this->metric->unit;

        if (! empty($this->spikeMetrics)) {
            $percentIncrease = round($this->spikeMetrics['percent_increase'] ?? 0, 1);
            $timeframeMinutes = $this->spikeMetrics['timeframe_minutes'] ?? 0;
            $oldValue = $this->spikeMetrics['old_value'] ?? 0;

            return __('The metric ":key" suddenly spiked by :percent% (from :old_value:unit to :new_value:unit) within :timeframe minutes.', [
                'key' => $key,
                'percent' => $percentIncrease,
                'old_value' => $oldValue,
                'new_value' => $value,
                'unit' => $unit,
                'timeframe' => $timeframeMinutes,
            ]);
        }

        return __('The metric ":key" suddenly spiked to :value:unit.', [
            'key' => $key,
            'value' => $value,
            'unit' => $unit,
        ]);
    }

    public static function info(): ?string
    {
        return __('Triggered when a metric suddenly spikes by a large percentage in a short timeframe.');
    }

    public function uniqueId(): string
    {
        return 'metric-spike-'.$this->metric->key;
    }

    public function site(): ?Site
    {
        return $this->metric->healthcheck?->site;
    }
}
