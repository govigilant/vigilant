<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('uptime'), 'Uptime')
    ->icon('tni-double-caret-up-circle-o')
    ->parent('health')
    ->gate('use-uptime')
    ->routeIs('uptime*')
    ->sort(1);
