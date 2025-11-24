<?php

namespace Vigilant\Healthchecks\Notifications\Conditions;

use Vigilant\Healthchecks\Enums\Status;
use Vigilant\Healthchecks\Notifications\HealthCheckFailedNotification;
use Vigilant\Notifications\Conditions\SelectCondition;
use Vigilant\Notifications\Notifications\Notification;

class StatusCondition extends SelectCondition
{
    public static string $name = 'Status';

    public function options(): array
    {
        return [
            Status::Unhealthy->value => 'Unhealthy',
            Status::Warning->value => 'Warning',
        ];
    }

    public function applies(Notification $notification, ?string $operand, ?string $operator, mixed $value, ?array $meta): bool
    {
        /** @var HealthCheckFailedNotification $notification */
        $status = Status::tryFrom($value);
        if ($status === null) {
            return false;
        }

        return $notification->healthcheck->results()
            ->where('status', '=', $status)
            ->exists();
    }
}
