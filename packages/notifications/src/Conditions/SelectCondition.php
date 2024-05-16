<?php

namespace Vigilant\Notifications\Conditions;

use Vigilant\Notifications\Enums\ConditionType;

abstract class SelectCondition extends Condition
{
    public ConditionType $type = ConditionType::Select;

    /** @return array<string, string> */
    abstract public function options(): array;
}
