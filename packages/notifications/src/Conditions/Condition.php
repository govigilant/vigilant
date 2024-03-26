<?php

namespace Vigilant\Notifications\Conditions;

use Vigilant\Notifications\Enums\ConditionType;

abstract class Condition
{
    public static string $name = '';

    public static ConditionType $type = ConditionType::Text;

    abstract public function applies(string $sku, ?string $operand, string $operator, ?string $value, ?string $meta): bool;

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
