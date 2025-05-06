<?php

namespace Vigilant\Uptime\Enums;

enum State: string
{
    case Up = 'up';
    case Retrying = 'retrying';
    case Down = 'down';
}
