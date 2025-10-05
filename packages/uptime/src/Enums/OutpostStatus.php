<?php

namespace Vigilant\Uptime\Enums;

enum OutpostStatus: string
{
    case Available = 'available';
    case Unavailable = 'unavailable';
}
