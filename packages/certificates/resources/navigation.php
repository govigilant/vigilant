<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('certificates'), 'Certificates')
    ->icon('phosphor-certificate')
    ->gate('use-certificates')
    ->routeIs('certificate*')
    ->sort(600);
