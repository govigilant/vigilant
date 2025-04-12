<?php

namespace Vigilant\Certificates\Notifications\Conditions;

use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Enums\ConditionType;
use Vigilant\Notifications\Notifications\Notification;

class DaysCondition extends Condition
{
    public static string $name = 'Expires within days';

    public ConditionType $type = ConditionType::Number;

    /** @param CertificateExpiredNotification $notification */
    public function applies(Notification $notification, ?string $operand, ?string $operator, mixed $value, ?array $meta): bool
    {
        $value = (int) $value;

        $validTo = $notification->monitor->valid_to;

        return $validTo->diffInDays(now()) <= $value
            && $validTo->diffInDays(now()) > 0;
    }
}
