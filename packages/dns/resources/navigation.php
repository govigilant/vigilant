<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('dns.index'), 'DNS')
    ->parent('infrastructure')
    ->icon('phosphor-globe-simple')
    ->gate('use-dns')
    ->routeIs('dns*')
    ->sort(400);
