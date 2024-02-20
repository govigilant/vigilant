<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('dashboard'), 'Dashboard')
    ->icon('tni-area-chart-alt-o');

Navigation::add(route('profile.show'), 'Profile')
    ->icon('tni-user-o')
    ->sort(20)
    ->children(function (Vigilant\Core\Navigation\Navigation $nav) {

        $nav->add(route('profile.show'), 'test');
        $nav->add(route('profile.show'), 'test');

    });
