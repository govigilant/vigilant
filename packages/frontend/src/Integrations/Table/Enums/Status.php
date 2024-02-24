<?php

namespace Vigilant\Frontend\Integrations\Table\Enums;

enum Status: string
{
    case Success = 'success';
    case Warning = 'warning';
    case Danger = 'danger';
}
