<?php

namespace Vigilant\Notifications\Enums;

enum ConditionType: string
{
    case Text = 'text';

    public function view(): string
    {
        return 'notifications::condition-builder.type.'.$this->value;
    }
}
