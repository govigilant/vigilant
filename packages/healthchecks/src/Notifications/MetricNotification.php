<?php

namespace Vigilant\Healthchecks\Notifications;

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
        public Metric $metric
    ) {}

    public function title(): string
    {
        $domain = $this->metric->healthcheck->domain;

        return __('Metric threshold exceeded for :domain', ['domain' => $domain]);
    }

    public function description(): string
    {
        $key = $this->metric->key;
        $value = $this->metric->value;
        $unit = $this->metric->unit;

        return __('The metric ":key" has exceeded its configured threshold with a value of :value:unit.', [
            'key' => $key,
            'value' => $value,
            'unit' => $unit,
        ]);
    }

    public static function info(): ?string
    {
        return __('Triggered when a healthcheck metric exceeds configured thresholds.');
    }

    public function uniqueId(): string
    {
        return $this->metric->key;
    }

    public function site(): ?Site
    {
        return $this->metric->healthcheck->site;
    }
}
