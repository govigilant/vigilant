<?php

namespace Vigilant\Notifications\Conditions;

use Vigilant\Notifications\Enums\ConditionType;
use Vigilant\Notifications\Notifications\Notification;

abstract class Condition
{
    public static string $name = '';

    public ConditionType $type = ConditionType::Text;

    abstract public function applies(Notification $notification, ?string $operand, string $operator, mixed $value, ?array $meta): bool;

    public function getOperators(): array
    {
        return [];
    }

    public function getOperands(): array
    {
        return [];
    }

    public function getMetadata(): array
    {
        return [];
    }
}
