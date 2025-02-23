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
        public float $currentAverage
    ) {}

    public function title(): string
    {
        $site = $this->site()?->url ?? $this->monitor->settings['host'] ?? '';

        return __(':site latency changed by :percent %', ['site' => $site, 'percent' => $this->percent]);
    }

    public function description(): string
    {
        return __('Past 12 hour average: :previous ms. Current average: :current ms', [
            'previous' => round($this->previousAverage, 2),
            'current' => round($this->currentAverage, 2),
        ]);
    }

    public function viewUrl(): ?string
    {
        return route('uptime.monitor.view', ['monitor' => $this->monitor]);
    }

    public function level(): Level
    {
        return match (true) {
            $this->percent < 10 => Level::Info,
            $this->percent < 25 => Level::Warning,
            default => Level::Critical,
        };
    }

    public function uniqueId(): string|int
    {
        return $this->monitor->id;
    }

    public function site(): ?Site
    {
        return $this->monitor->site;
    }
}
