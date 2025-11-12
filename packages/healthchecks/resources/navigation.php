<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('healthchecks.index'), 'Healthchecks')
    ->icon('phosphor-heartbeat')
    ->parent('health')
    ->gate('use-healthchecks')
    ->routeIs('healthchecks*')
    ->sort(2);
