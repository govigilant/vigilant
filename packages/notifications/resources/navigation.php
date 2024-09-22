<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('notifications'), 'Notifications')
    ->icon('tni-exclamation-circle-o')
    ->sort(1000)
    ->children(function (\Vigilant\Core\Navigation\Navigation $navigation): void {

        $navigation->add(route('notifications.channels'), 'Notification Channels')
            ->routeIs('notifications.channel*')
            ->sort(1001); // TODO: Fix child menu

        $navigation->add(route('notifications.history'), 'Notification History')
            ->routeIs('notifications.history')
            ->sort(1002); // TODO: Fix child menu

    });
