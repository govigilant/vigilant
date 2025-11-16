<?php

namespace Vigilant\Healthchecks\Notifications\Conditions;

use Vigilant\Healthchecks\Notifications\DiskUsageNotification;
use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Enums\ConditionType;
use Vigilant\Notifications\Notifications\Notification;

class DiskFullInCondition extends Condition
{
    public static string $name = 'Disk full in (hours)';

    public ConditionType $type = ConditionType::Number;

    public function metadata(): array
    {
        return [];
    }

    public function applies(Notification $notification, ?string $operand, ?string $operator, mixed $value, ?array $meta): bool
    {
        /** @var DiskUsageNotification $notification */
        $hoursUntilFull = $notification->hoursUntilFull;

        $result = match ($operator) {
            '>' => $hoursUntilFull > $value,
            '>=' => $hoursUntilFull >= $value,
            '<' => $hoursUntilFull < $value,
            '<=' => $hoursUntilFull <= $value,
            '=' => $hoursUntilFull == $value,
            '!=' => $hoursUntilFull != $value,
            default => false,
        };

        return $result;
    }
}
