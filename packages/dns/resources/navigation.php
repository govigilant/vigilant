<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('dns.index'), 'DNS')
    ->icon('phosphor-globe-simple')
    ->routeIs('dns*')
    ->sort(400);
