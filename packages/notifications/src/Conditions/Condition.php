<?php

namespace Vigilant\Notifications\Conditions;

use Vigilant\Notifications\Enums\ConditionType;
use Vigilant\Notifications\Notifications\Notification;

abstract class Condition
{
    public static string $name = '';

    public ConditionType $type = ConditionType::Text;

    abstract public function applies(Notification $notification, ?string $operand, string $operator, mixed $value, ?array $meta): bool;

    public function operators(): array
    {
        return [];
    }

    public function operands(): array
    {
        return [];
    }

    public function metadata(): array
    {
        return [];
    }
}
