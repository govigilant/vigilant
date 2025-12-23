<?php

return [
    'queue' => 'uptime',

    'intervals' => [
        1 => 'Every second',
        5 => 'Every 5 seconds',
        10 => 'Every 10 seconds',
        20 => 'Every 20 seconds',
        30 => 'Every 30 seconds',
        60 => 'Every minute',
        300 => 'Every 5 minutes',
        600 => 'Every 10 minutes',
        3600 => 'Every hour',
    ],

    'allow_external_outposts' => env('UPTIME_ALLOW_EXTERNAL_OUTPOSTS', false),
    'outpost_secret' => env('UPTIME_OUTPOST_SECRET', 'outpost-secret'),
    'ip_geo_overrides' => json_decode(env('UPTIME_IP_GEO_OVERRIDES', '{}'), true),
];
