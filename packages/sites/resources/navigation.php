<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('sites'), 'Sites')
    ->routeIs('site*')
    ->icon('tni-hd-screen-o')
    ->sort(100);
