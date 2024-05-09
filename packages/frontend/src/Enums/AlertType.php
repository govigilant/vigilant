<?php

namespace Vigilant\Frontend\Enums;

enum AlertType: string
{
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Danger = 'danger';

    public function component(): string
    {
        return 'alerts.' . $this->value;
    }
}
