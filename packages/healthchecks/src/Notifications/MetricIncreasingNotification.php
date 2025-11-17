<?php

namespace Vigilant\Healthchecks\Notifications;

use Vigilant\Healthchecks\Models\Healthcheck;
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
                'operator' => '<=',
                'value' => 5,
            ],
        ],
    ];

    public function __construct(
        public Healthcheck $healthcheck,
        public int $runId,
        public array $increasedMetrics = []
    ) {}

    public function title(): string
    {
        $host = $this->healthcheck->site->url ?? $this->healthcheck->domain;

        return __('Metric increasing for :host', ['host' => $host]);
    }

    public function description(): string
    {
        $metricsInfo = collect($this->increasedMetrics)
            ->map(function (array $data): string {
                $unit = $data['unit'] ? ' '.$data['unit'] : '';
                $percentIncrease = round($data['percent_increase'], 2);

                return $data['key'].': '.$data['old_value'].$unit.' → '.$data['new_value'].$unit.' (+'.$percentIncrease.'%)';
            })->implode(PHP_EOL);

        return $metricsInfo;
    }

    public static function info(): ?string
    {
        return __('Triggered when a metric increases by a specified percentage within a timeframe.');
    }

    public function uniqueId(): string
    {
        return 'metric-increasing-'.$this->healthcheck->id.'-'.$this->runId;
    }

    public function site(): ?Site
    {
        return $this->healthcheck->site;
    }
}
