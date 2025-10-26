<?php

use Vigilant\Certificates\Models\CertificateMonitorHistory;
use Vigilant\Dns\Models\DnsMonitorHistory;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Notifications\Models\History;
use Vigilant\Uptime\Models\Downtime;
use Vigilant\Uptime\Models\Result;

return [
    'edition' => env('EDITION', 'ce'),

    'user_agent' => 'Vigilant Bot',

    'data_retention' => [
        DnsMonitorHistory::class => env('DATA_RETENTION_DNS_MONITOR_HISTORY', 180),
        Downtime::class => env('DATA_RETENTION_DOWNTIME', 730),
        Result::class => env('DATA_RETENTION_UPTIME_RESULT', 180),
        LighthouseResult::class => env('DATA_RETENTION_LIGHTHOUSE', 180),
        History::class => env('DATA_RETENTION_NOTIFICATION_HISTORY', 90),
        CertificateMonitorHistory::class => env('DATA_RETENTION_CERTIFICATE_MONITOR_HISTORY', 180),
    ],
];
