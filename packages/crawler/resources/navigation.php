<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('crawler.index'), 'Crawler')
    ->icon('lineawesome-spider-solid')
    ->routeIs('crawler*')
    ->sort(500);
