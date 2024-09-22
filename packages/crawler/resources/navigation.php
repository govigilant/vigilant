<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('crawler.index'), 'Link Issues')
    ->icon('lineawesome-spider-solid')
    ->gate('use-crawler')
    ->routeIs('crawler*')
    ->sort(500);
