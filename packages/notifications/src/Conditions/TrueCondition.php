<?php

namespace Vigilant\Notifications\Conditions;

use Vigilant\Notifications\Notifications\Notification;

class TrueCondition extends Condition
{
    public static string $name = 'TRUE';

    public function applies(
        Notification $notification,
        ?string $operand,
        string $operator,
        mixed $value,
        ?array $meta
    ): bool {
        return true;
    }

    public function getOperators(): array
    {
        return [
            '=',
            '!=',
        ];
    }

    public function getOperands(): array
    {
        return [
            'operand-a',
            'operand-b',
        ];
    }
}
