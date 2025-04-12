<?php

namespace Vigilant\Notifications\Conditions;

use Vigilant\Notifications\Enums\ConditionType;
use Vigilant\Notifications\Notifications\Notification;

abstract class Condition
{
    public static string $name = '';

    public ConditionType $type = ConditionType::Text;

    /** @param array<string, mixed> $meta */
    abstract public function applies(Notification $notification, ?string $operand, ?string $operator, mixed $value, ?array $meta): bool;

    /** @return array<string, string> */
    public function operators(): array
    {
        return [];
    }

    /** @return array<string, string> */
    public function operands(): array
    {
        return [];
    }

    /** @return array<string, mixed> */
    public function metadata(): array
    {
        return [];
    }
}
