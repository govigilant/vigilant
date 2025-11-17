<?php

namespace Vigilant\Healthchecks\Notifications;

use Vigilant\Healthchecks\Enums\Status;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Models\Result;
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
        /** @var \Illuminate\Database\Eloquent\Collection<int, Result> $results */
        $results = $this->healthcheck->results()
            ->where('run_id', '=', $this->runId)
            ->where('status', '!=', Status::Healthy)
            ->get();

        $failedChecks = $results->map(function (Result $result): string {
            if ($result->message === null) {
                return $result->key;
            }

            return $result->key.': '.$result->message;
        })->implode(PHP_EOL);

        return __('Healthchecks have failed: :checks', ['checks' => $failedChecks]);
    }

    public static function info(): ?string
    {
        return __('Triggered when a healthcheck fails.');
    }

    public function uniqueId(): string
    {
        return $this->healthcheck->id.'-'.$this->runId;
    }

    public function site(): ?Site
    {
        return $this->healthcheck->site;
    }
}
