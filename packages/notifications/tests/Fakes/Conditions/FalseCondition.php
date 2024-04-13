<?php

namespace Vigilant\Notifications\Tests\Fakes\Conditions;

use Vigilant\Notifications\Conditions\Condition;
use Vigilant\Notifications\Notifications\Notification;

class FalseCondition extends Condition
{
    public function applies(Notification $notification, ?string $operand, string $operator, mixed $value, ?array $meta): bool
    {
        return false;
    }
}
