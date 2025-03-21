<?php

return [
    'queue' => 'lighthouse',

    'intervals' => [
        60 => 'Hourly',
        60 * 3 => 'Every 3 hours',
        60 * 6 => 'Every 6 hours',
        60 * 12 => 'Every 12 hours',
        60 * 24 => 'Daily',
        60 * 24 * 7 => 'Weekly',
    ],

    'runs' => env('LIGHTHOUSE_RUNS', 3),

    'workers' => explode(',', env('LIGHTHOUSE_WORKERS', 'lighthouse')),

    /* URL to Vigilant */
    'lighthouse_app_url' => env('LIGHTHOUSE_APP_URL', 'http://app:8000'),
];
