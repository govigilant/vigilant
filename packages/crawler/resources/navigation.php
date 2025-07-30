<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('crawler.index'), 'Link Issues')
    ->parent('health')
    ->icon('carbon-text-link')
    ->gate('use-crawler')
    ->routeIs('crawler*')
    ->sort(3);
