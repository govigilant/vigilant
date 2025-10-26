<?php

use Vigilant\Core\Facades\Navigation;

Navigation::add(null, 'Notifications')
    ->code('notifications')
    ->routeIs('notifications*')
    ->icon('tni-exclamation-circle-o')
    ->sort(1000);

Navigation::add(route('notifications'), 'Notification Types')
    ->parent('notifications')
    ->icon('phosphor-list-heart-duotone')
    ->routeIs('notifications', 'notifications.trigger.*')
    ->sort(1);

Navigation::add(route('notifications.channels'), 'Notification Channels')
    ->parent('notifications')
    ->icon('phosphor-chat-centered-dots-bold')
    ->routeIs('notifications.channel*')
    ->sort(2);

Navigation::add(route('notifications.history'), 'Notification History')
    ->parent('notifications')
    ->icon('tni-history-o')
    ->routeIs('notifications.history')
    ->sort(3);
