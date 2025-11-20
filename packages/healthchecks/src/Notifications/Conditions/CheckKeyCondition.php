<?php

namespace Vigilant\Healthchecks\Notifications\Conditions;

use Vigilant\Healthchecks\Models\Result;
use Vigilant\Healthchecks\Notifications\HealthCheckFailedNotification;
use Vigilant\Notifications\Conditions\SelectCondition;
use Vigilant\Notifications\Notifications\Notification;

class CheckKeyCondition extends SelectCondition
{
    public static string $name = 'Healthcheck';

    public function options(): array
    {
        return Result::query()
            ->whereNotNull('key')
            ->distinct('key')
            ->orderBy('key')
            ->pluck('key', 'key')
            ->toArray();
    }

    public function applies(Notification $notification, ?string $operand, ?string $operator, mixed $value, ?array $meta): bool
    {
        /** @var HealthCheckFailedNotification $notification */
        return $notification->healthcheck->results()
            ->where('run_id', '=', $notification->runId)
            ->where('key', '=', $value)
            ->exists();
    }
}
