<?php

namespace Vigilant\Healthchecks\Notifications;

use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Models\Metric;
use Vigilant\Healthchecks\Notifications\Conditions\MetricUnitCondition;
use Vigilant\Healthchecks\Notifications\Conditions\MetricValueCondition;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class MetricNotification extends Notification implements HasSite
{
    public static string $name = 'Metric threshold exceeded';

    public Level $level = Level::Warning;

    public static ?int $defaultCooldown = 60;

    public static array $defaultConditions = [
        'type' => 'group',
        'operator' => 'all',
        'children' => [
            [
                'type' => 'condition',
                'condition' => MetricUnitCondition::class,
                'value' => '%',
            ],
            [
                'type' => 'condition',
                'condition' => MetricValueCondition::class,
                'operator' => '>',
                'value' => 90,
            ],
        ],
    ];

    public function __construct(
        public Healthcheck $healthcheck,
        public int $runId
    ) {}

    public function title(): string
    {
        $host = $this->healthcheck->site->url ?? $this->healthcheck->domain;

        return __('Metric threshold exceeded for :host', ['host' => $host]);
    }

    public function description(): string
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Metric> $metricsCollection */
        $metricsCollection = $this->healthcheck->metrics()
            ->where('run_id', '=', $this->runId)
            ->get();

        $metrics = $metricsCollection->map(function (Metric $metric): string {
            $unit = $metric->unit ? ' '.$metric->unit : '';

            return $metric->key.': '.$metric->value.$unit;
        })->implode(PHP_EOL);

        return __('Run ID: :runId', ['runId' => $this->runId]).PHP_EOL.PHP_EOL.$metrics;
    }

    public static function info(): ?string
    {
        return __('Triggered when a healthcheck metric exceeds configured thresholds.');
    }

    public function uniqueId(): string
    {
        return 'metric-'.$this->healthcheck->id.'-'.$this->runId;
    }

    public function site(): ?Site
    {
        return $this->healthcheck->site;
    }
}
