<?php

namespace Vigilant\Uptime\Uptime;

use Illuminate\Http\Client\ConnectionException;
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

        try {
            $response = HttpClient::timeout($monitor->timeout)
                ->connectTimeout($monitor->timeout)
                ->withUserAgent('Vigilant Bot')
                ->get($settings['host']);
        } catch (ConnectionException) {
            return new UptimeResult(
                false,
            );
        }

        $stats = $response->handlerStats();

        return new UptimeResult(
            true,
            $stats['total_time'] ?? 0
        );
    }
}