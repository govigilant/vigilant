<?php

namespace Vigilant\Healthchecks\Enums;

enum Status: string
{
    case Healthy = 'healthy';
    case Warning = 'warning';
    case Unhealthy = 'unhealthy';
}
