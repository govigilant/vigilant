<?php

namespace Vigilant\Healthchecks\Notifications\Conditions;

use Vigilant\Healthchecks\Notifications\HealthCheckFailedNotification;
use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Enums\ConditionType;
use Vigilant\Notifications\Notifications\Notification;

class CheckKeyCondition extends Condition
{
    public static string $name = 'Check key';

    public ConditionType $type = ConditionType::Select;

    public function metadata(): array
    {
        return [
            'options' => [
                'endpoint_check' => 'Endpoint Check',
                'connection' => 'Connection',
                'database' => 'Database',
                'cache' => 'Cache',
                'queue' => 'Queue',
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
            if ($result->key === $value) {
                return true;
            }
        }

        return false;
    }
}
