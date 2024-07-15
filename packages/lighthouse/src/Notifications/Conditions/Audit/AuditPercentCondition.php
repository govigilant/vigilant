<?php

namespace Vigilant\Lighthouse\Notifications\Conditions\Audit;

use Vigilant\Lighthouse\Notifications\NumericAuditChangedNotification;
use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Notifications\Notification;

class AuditPercentCondition extends Condition
{
    public static string $name = 'Percent change';

    public function operands(): array
    {
        return [
            'relative' => 'Relative',
            'absolute' => 'Absolute',
        ];
    }

    public function operators(): array
    {
        return [
            '=' => 'Equal to',
            '<>' => 'Not equal to',
            '<' => 'Less than',
            '<=' => 'Less or equal than',
            '>' => 'Greater than',
            '>=' => 'Greater or equal than',
        ];
    }

    public function applies(
        Notification $notification,
        ?string $operand,
        string $operator,
        mixed $value,
        ?array $meta
    ): bool {
        /** @var NumericAuditChangedNotification $notification */
        $percent = $notification->percentChanged;

        if ($operand === 'absolute') {
            $percent = abs($percent);
        }

        return match ($operator) {
            '=' => $percent == $value,
            '<>' => $percent != $value,
            '<' => $percent < $value,
            '<=' => $percent <= $value,
            '>' => $percent > $value,
            '>=' => $percent >= $value,
            default => false,
        };
    }
}
