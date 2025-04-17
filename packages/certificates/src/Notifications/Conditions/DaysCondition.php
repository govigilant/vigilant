<?php

namespace Vigilant\Certificates\Notifications\Conditions;

use Vigilant\Certificates\Notifications\CertificateExpiredNotification;
use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Enums\ConditionType;
use Vigilant\Notifications\Notifications\Notification;

class DaysCondition extends Condition
{
    public static string $name = 'Expires within days';

    public ConditionType $type = ConditionType::Number;

    public function applies(Notification $notification, ?string $operand, ?string $operator, mixed $value, ?array $meta): bool
    {
        /** @var CertificateExpiredNotification $notification */
        $value = (int) $value;

        $validTo = $notification->monitor->valid_to;

        if ($validTo === null) {
            return false;
        }

        return $validTo->diffInDays(now()) <= $value
            && $validTo->diffInDays(now()) > 0;
    }
}
