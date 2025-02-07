<?php

return [
    'queue' => 'dns',

    'nameservers' => env('DNS_NAMESERVERS', '1.1.1.1,1.0.0.1,9.9.9.9,8.8.8.8'),

    'max_attempts' => env('DNS_MAX_ATTEMPTS', 3),
];
