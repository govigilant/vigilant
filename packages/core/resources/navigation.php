<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(null, 'Health')
    ->code('health')
    ->icon('phosphor-heart-half-duotone')
    ->sort(200);

Navigation::add(null, 'Performance')
    ->code('performance')
    ->icon('carbon-chart-line-smooth')
    ->sort(300);

Navigation::add(null, 'Infrastructure')
    ->code('infrastructure')
    ->icon('carbon-cloud-monitoring')
    ->sort(400);
