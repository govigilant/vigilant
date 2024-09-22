<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('uptime'), 'Uptime')
    ->icon('tni-double-caret-up-circle-o')
    ->gate('use-uptime')
    ->routeIs('uptime*')
    ->sort(200);
