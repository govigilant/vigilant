<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('lighthouse'), 'Lighthouse')
    ->parent('performance')
    ->icon('phosphor-lighthouse-light')
    ->gate('use-lighthouse')
    ->routeIs('lighthouse*')
    ->sort(300);
