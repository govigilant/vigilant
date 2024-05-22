<?php

namespace Vigilant\Uptime\Uptime;

use Illuminate\Support\Facades\Http as HttpClient;
use Illuminate\Support\Facades\Validator;
use Vigilant\Uptime\Data\UptimeResult;
use Vigilant\Uptime\Models\Monitor;

class Http extends UptimeMonitor
{
    public function process(Monitor $monitor): UptimeResult
    {
        $settings = Validator::validate($monitor->settings, [
            'host' => ['required', 'url'],
        ]);

        $response = HttpClient::timeout($monitor->timeout)
            ->connectTimeout($monitor->timeout)
            ->withUserAgent('Vigilant Bot')
            ->get($settings['host']);

        if (! $response->ok()) {
            return new UptimeResult(
                false,
                data: [
                    'status' => $response->status(),
                ],
            );
        }

        $stats = $response->handlerStats();

        return new UptimeResult(
            true,
            $stats['total_time'] ?? 0,
            ['status' => $response->status()],
        );
    }
}
