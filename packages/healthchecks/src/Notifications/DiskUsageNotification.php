<?php

namespace Vigilant\Healthchecks\Notifications;

use Illuminate\Support\Carbon;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Notifications\Conditions\DiskFullInCondition;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class DiskUsageNotification extends Notification implements HasSite
{
    public static string $name = 'Disk usage';

    public Level $level = Level::Critical;

    public static ?int $defaultCooldown = 60;

    public static array $defaultConditions = [
        'type' => 'group',
        'operator' => 'any',
        'children' => [
            [
                'type' => 'condition',
                'condition' => DiskFullInCondition::class,
                'operator' => '<=',
                'value' => 24,
            ],
        ],
    ];

    public function __construct(
        public Healthcheck $healthcheck,
        public int $runId,
        public float $currentUsage,
        public float $velocity,
        public float $hoursUntilFull,
        public ?Carbon $estimatedFullAt = null
    ) {}

    public function title(): string
    {
        $domain = $this->healthcheck->domain;

        return __('Disk usage critical for :domain', ['domain' => $domain]);
    }

    public function description(): string
    {
        $hours = round($this->hoursUntilFull, 1);
        $velocityPerHour = round($this->velocity, 2);

        $message = __('Current disk usage: :usage%', ['usage' => round($this->currentUsage, 2)]).PHP_EOL;
        $message .= __('Growth rate: :rate% per hour', ['rate' => $velocityPerHour]).PHP_EOL;
        $message .= __('Estimated to reach 100% in: :hours hours', ['hours' => $hours]).PHP_EOL;

        if ($this->estimatedFullAt) {
            $message .= __('Estimated full at: :time', ['time' => teamTimezone($this->estimatedFullAt)->toDateTimeString()]);
        }

        return $message;
    }

    public static function info(): ?string
    {
        return __('Triggered when disk usage is projected to reach 100% within a specified timeframe.');
    }

    public function uniqueId(): string
    {
        return 'disk-usage-'.$this->healthcheck->id.'-'.$this->runId;
    }

    public function site(): ?Site
    {
        return $this->healthcheck->site;
    }
}
