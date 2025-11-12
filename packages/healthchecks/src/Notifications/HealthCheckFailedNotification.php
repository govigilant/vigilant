<?php

namespace Vigilant\Healthchecks\Notifications;

use Vigilant\Healthchecks\Enums\Status;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Notifications\Conditions\StatusCondition;
use Vigilant\Notifications\Contracts\HasSite;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Sites\Models\Site;

class HealthCheckFailedNotification extends Notification implements HasSite
{
    public static string $name = 'Healthcheck failed';

    public Level $level = Level::Critical;

    public static ?int $defaultCooldown = 60;

    public static array $defaultConditions = [
        'type' => 'group',
        'operator' => 'any',
        'children' => [
            [
                'type' => 'condition',
                'condition' => StatusCondition::class,
                'value' => Status::Unhealthy->value,
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

        return __('Healthcheck failed for :host', ['host' => $host]);
    }

    public function description(): string
    {
        return __('Run ID: :runId', ['runId' => $this->runId]);
    }

    public static function info(): ?string
    {
        return __('Triggered when a healthcheck detects unhealthy checks.');
    }

    public function uniqueId(): string
    {
        return $this->healthcheck->id . '-' . $this->runId;
    }

    public function site(): ?Site
    {
        return $this->healthcheck->site;
    }
}
