<?php

return [
    'queue' => 'crawler',

    'timeout' => env('CRAWLER_TIMEOUT', 5),
    'connect_timeout' => env('CRAWLER_CONNECT_TIMEOUT', 2),

    'crawls_per_minute' => (int) env('CRAWLER_CRAWLS_PER_MINUTE', 500),
];
