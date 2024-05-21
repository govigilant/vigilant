<?php

namespace Vigilant\Uptime\Uptime;

use Illuminate\Http\Client\HttpClientException;
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
                ->get($settings['host'])
                ->throw();
        } catch (HttpClientException $exception) {
            return new UptimeResult(
                false,
                data: [
                    'status' => $exception->getCode(),
                ],
            );
        }

        $stats = $response->handlerStats();

        return new UptimeResult(
            true,
            $stats['total_time'] ?? 0,
            [
                'status' => $response->status()
            ],
        );
    }
}
