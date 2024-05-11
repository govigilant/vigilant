<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('lighthouse'), 'Lighthouse')
    ->icon('phosphor-lighthouse-light')
    ->routeIs('lighthouse*')
    ->sort(300);
