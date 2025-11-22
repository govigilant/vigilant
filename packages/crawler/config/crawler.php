<?php

return [
    'queue' => 'crawler',

    'timeout' => env('CRAWLER_TIMEOUT', 5),
    'connect_timeout' => env('CRAWLER_CONNECT_TIMEOUT', 2),
];
