<?php

namespace Vigilant\Notifications\Enums;

enum ConditionType: string
{
    case Text = 'text';
    case Number = 'number';
    case Select = 'select';
    case Static = 'static';

    public function view(): string
    {
        return 'notifications::condition-builder.type.'.$this->value;
    }
}
