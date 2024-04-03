<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(route('notifications'), 'Notifications')
    ->icon('tni-exclamation-circle-o')
    ->sort(400)
    ->children(function(\Vigilant\Core\Navigation\Navigation $navigation): void {

        $navigation->add(route('notifications.channels'), 'Notification Channels')
            ->sort(401); // TODO: Fix child menu

    });
