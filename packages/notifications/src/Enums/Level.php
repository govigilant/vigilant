<?php

namespace Vigilant\Notifications\Enums;

enum Level: string
{
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Critical = 'critical';
}
