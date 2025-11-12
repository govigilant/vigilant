<?php

return [
    'queue' => 'healthchecks',

    'http_timeout' => env('HEALTHCHECKS_HTTP_TIMEOUT', 10),

    'intervals' => [
        30 => 'Every 30 seconds',
        60 => 'Every minute',
        300 => 'Every 5 minutes',
        600 => 'Every 10 minutes',
        1800 => 'Every 30 minutes',
        3600 => 'Every hour',
        21600 => 'Every 6 hours',
        43200 => 'Every 12 hours',
        86400 => 'Daily',
    ],
];
