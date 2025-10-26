<?php

namespace Vigilant\Uptime\Notifications;

use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Notifications\Conditions\LatencyPercentCondition;

class LatencyChangedNotification extends Notification implements HasSite
{
    public static string $name = 'Latency Changed';

    public static ?int $defaultCooldown = 60 * 24;

    public Level $level = Level::Warning;

    public static array $defaultConditions = [
        'type' => 'group',
        'operator' => 'all',
        'children' => [
            [
                'type' => 'condition',
                'condition' => LatencyPercentCondition::class,
                'operator' => '>=',
                'operand' => 'absolute',
                'value' => 50,
            ],
        ],
    ];

    public function __construct(
        public Monitor $monitor,
        public float $percent,
        public float $previousAverage,
        public float $currentAverage,
        public ?string $country = null
    ) {}

    public function title(): string
    {
        $site = $this->site()->url ?? $this->monitor->settings['host'] ?? '';
        $country = $this->country ? " in {$this->country}" : '';

        return __(':site latency changed by :percent % from :country', ['site' => $site, 'percent' => $this->percent, 'country' => $country]);
    }

    public function description(): string
    {
        if ($this->country) {
            return __('Past 12 hour average: :previous ms. Current average: :current ms from :country', [
                'previous' => round($this->previousAverage, 2),
                'current' => round($this->currentAverage, 2),
                'country' => $this->country,
            ]);
        } else {
            return __('Past 12 hour average: :previous ms. Current average: :current ms', [
                'previous' => round($this->previousAverage, 2),
                'current' => round($this->currentAverage, 2),
            ]);
        }
    }

    public static function info(): ?string
    {
        return __('Triggered after an uptime check if the latency has changed.');
    }

    public function viewUrl(): ?string
    {
        return route('uptime.monitor.view', ['monitor' => $this->monitor]);
    }

    public function level(): Level
    {
        return match (true) {
            $this->percent < 100 => Level::Info,
            $this->percent < 200 => Level::Warning,
            default => Level::Critical,
        };
    }

    public function uniqueId(): string|int
    {
        return $this->country
            ? "{$this->monitor->id}_{$this->country}"
            : $this->monitor->id;
    }

    public function site(): ?Site
    {
        return $this->monitor->site;
    }
}
