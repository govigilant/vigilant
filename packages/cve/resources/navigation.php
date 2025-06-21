<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('cve.index'), 'CVEs')
    ->parent('infrastructure')
    ->icon('phosphor-shield-star')
    ->gate('use-cve')
    ->routeIs('cve*')
    ->sort(700);
