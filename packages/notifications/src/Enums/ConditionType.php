<?php

namespace Vigilant\Notifications\Enums;

enum ConditionType: string
{
    case Text = 'text';
    case Select = 'select';

    public function view(): string
    {
        return 'notifications::condition-builder.type.'.$this->value;
    }
}
