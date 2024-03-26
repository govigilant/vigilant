<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('notifications'), 'Notifications')
    ->icon('tni-exclamation-circle-o')
    ->sort(400);
