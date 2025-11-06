<?php

namespace Vigilant\Healthchecks\Enums;

enum Type: string
{
    case Endpoint = 'endpoint';
    case Module = 'module';
}
