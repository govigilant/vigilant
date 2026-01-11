<?php

namespace Vigilant\Lighthouse\Notifications\Conditions\Audit;

use Vigilant\Lighthouse\Notifications\NumericAuditChangedNotification;
use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Notifications\Notification;

class AuditDecreasesCondition extends Condition
{
    public static string $name = 'Audit value decreases';

    public function operands(): array
    {
        return [];
    }

    public function operators(): array
    {
        return [
            '>=' => 'By at least',
            '>' => 'By more than',
        ];
    }

    public function applies(
        Notification $notification,
        ?string $operand,
        ?string $operator,
        mixed $value,
        ?array $meta
    ): bool {
        /** @var NumericAuditChangedNotification $notification */
        $percentChange = $notification->percentChanged;

        return match ($operator) {
            '>' => $percentChange < -$value,
            '>=' => $percentChange <= -$value,
            default => false,
        };
    }
}
