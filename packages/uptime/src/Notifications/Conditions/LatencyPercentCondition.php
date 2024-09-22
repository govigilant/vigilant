<?php

namespace Vigilant\Uptime\Notifications\Conditions;

use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Uptime\Notifications\LatencyChangedNotification;

class LatencyPercentCondition extends Condition
{
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
        /** @var LatencyChangedNotification $notification */
        $percentChanged = $notification->percent;

        if ($operand === 'absolute') {
            $percentChanged = abs($percentChanged);
        }

        return match ($operator) {
            '=' => $percentChanged == $value,
            '<>' => $percentChanged != $value,
            '<' => $percentChanged < $value,
            '<=' => $percentChanged <= $value,
            '>' => $percentChanged > $value,
            '>=' => $percentChanged >= $value,
            default => false,
        };
    }
}
