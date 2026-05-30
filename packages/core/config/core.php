<?php

use Vigilant\Certificates\Models\CertificateMonitorHistory;
use Vigilant\Dns\Models\DnsMonitorHistory;
use Vigilant\Healthchecks\Models\Metric;
use Vigilant\Healthchecks\Models\Result as HealthcheckResult;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Notifications\Models\History;
use Vigilant\Uptime\Models\Downtime;
use Vigilant\Uptime\Models\Result;

return [
    'edition' => env('EDITION', 'ce'),

    'user_agent' => 'Vigilant Bot',

    'ssrf' => [
        /*
         * Hostnames that should be exempt from SSRF protection. Useful for
         * self-hosted operators who legitimately monitor internal services.
         * Comma-separated list (e.g. "internal.api.local,10.0.0.5").
         */
        'allowed_hosts' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('SSRF_ALLOWED_HOSTS', '')),
        ))),

        /*
         * Additional CIDR ranges to block beyond the built-in defaults
         * (RFC1918, loopback, link-local, CGN, cloud metadata, etc.).
         */
        'extra_blocked_cidrs' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('SSRF_BLOCKED_CIDRS', '')),
        ))),
    ],

    'data_retention' => [
        DnsMonitorHistory::class => env('DATA_RETENTION_DNS_MONITOR_HISTORY', 180),
        Downtime::class => env('DATA_RETENTION_DOWNTIME', 730),
        Result::class => env('DATA_RETENTION_UPTIME_RESULT', 180),
        LighthouseResult::class => env('DATA_RETENTION_LIGHTHOUSE', 180),
        History::class => env('DATA_RETENTION_NOTIFICATION_HISTORY', 90),
        CertificateMonitorHistory::class => env('DATA_RETENTION_CERTIFICATE_MONITOR_HISTORY', 180),
        HealthcheckResult::class => env('DATA_RETENTION_HEALTHCHECK_RESULT', 180),
        Metric::class => env('DATA_RETENTION_HEALTHCHECK_METRIC', 180),
    ],
];
