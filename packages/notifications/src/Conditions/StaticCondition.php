<?php

namespace Vigilant\Notifications\Conditions;

use Vigilant\Notifications\Enums\ConditionType;

abstract class StaticCondition extends Condition
{
    public ConditionType $type = ConditionType::Static;

    public function operators(): array
    {
        return [];
    }
}
