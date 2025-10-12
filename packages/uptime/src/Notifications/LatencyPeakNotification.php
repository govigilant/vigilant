<?php

namespace Vigilant\Uptime\Notifications;

use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Notifications\Conditions\LatencyPercentCondition;

class LatencyPeakNotification extends Notification implements HasSite
{
    public static string $name = 'Latency Peak';

    public static ?int $defaultCooldown = 60 * 6; // 6 hours

    public Level $level = Level::Warning;

    public static array $defaultConditions = [
        'type' => 'group',
        'children' => [
            [
                'type' => 'condition',
                'condition' => LatencyPercentCondition::class,
                'operator' => '>=',
                'operand' => 'absolute',
                'value' => 100,
            ],
        ],
    ];

    public function __construct(
        public Monitor $monitor,
        public float $peakLatency,
        public float $averageLatency,
        public float $percent,
        public ?string $country = null
    ) {}

    public function title(): string
    {
        $site = $this->site()->url ?? $this->monitor->settings['host'] ?? '';
        $country = $this->country ? " in {$this->country}" : '';

        return __(':site latency is peaking from :country', ['site' => $site, 'country' => $country]);
    }

    public function description(): string
    {
        $country = $this->country ? " ({$this->country})" : '';

        return __('Current peak: :peak ms. Average: :average ms (+:percent%) from :country', [
            'peak' => round($this->peakLatency, 2),
            'average' => round($this->averageLatency, 2),
            'percent' => round($this->percent, 0),
            'country' => $country,
        ]);
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
            ? "peak_{$this->monitor->id}_{$this->country}"
            : "peak_{$this->monitor->id}";
    }

    public function site(): ?Site
    {
        return $this->monitor->site;
    }
}
