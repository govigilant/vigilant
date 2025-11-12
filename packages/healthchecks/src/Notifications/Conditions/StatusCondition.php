<?php

namespace Vigilant\Healthchecks\Notifications\Conditions;

use Vigilant\Healthchecks\Enums\Status;
use Vigilant\Healthchecks\Notifications\HealthCheckFailedNotification;
use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Enums\ConditionType;
use Vigilant\Notifications\Notifications\Notification;

class StatusCondition extends Condition
{
    public static string $name = 'Status';

    public ConditionType $type = ConditionType::Select;

    public function metadata(): array
    {
        return [
            'options' => [
                Status::Unhealthy->value => 'Unhealthy',
                Status::Warning->value => 'Warning',
                'failed' => 'Failed',
            ],
        ];
    }

    public function applies(Notification $notification, ?string $operand, ?string $operator, mixed $value, ?array $meta): bool
    {
        /** @var HealthCheckFailedNotification $notification */
        /** @var \Illuminate\Database\Eloquent\Collection<int, \Vigilant\Healthchecks\Models\Result> $results */
        $results = $notification->healthcheck->results()
            ->where('run_id', $notification->runId)
            ->get();

        foreach ($results as $result) {
            if ($result->status === $value || $result->status === Status::from($value)->value) {
                return true;
            }
        }

        return false;
    }
}
