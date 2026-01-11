<?php

namespace Vigilant\Lighthouse\Notifications\Conditions\Audit;

use Vigilant\Lighthouse\Notifications\NumericAuditChangedNotification;
use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Notifications\Notification;

class AuditValueCondition extends Condition
{
    public static string $name = 'Audit value';

    public function operands(): array
    {
        return [
            'new' => 'New value',
            'old' => 'Old value',
        ];
    }

    public function operators(): array
    {
        return [
            '=' => 'Equal to',
            '<>' => 'Not equal to',
            '<' => 'Less than',
            '<=' => 'Less than or equal to',
            '>' => 'Greater than',
            '>=' => 'Greater than or equal to',
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
        $auditValue = $operand === 'old'
            ? $notification->previous
            : $notification->current;

        return match ($operator) {
            '=' => $auditValue == $value,
            '<>' => $auditValue != $value,
            '<' => $auditValue < $value,
            '<=' => $auditValue <= $value,
            '>' => $auditValue > $value,
            '>=' => $auditValue >= $value,
            default => false,
        };
    }
}
