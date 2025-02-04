<?php

namespace Vigilant\Uptime\Uptime;

use GuzzleHttp\Exception\ConnectException;
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
                ->withOptions(['verify' => false])
                ->withUserAgent(config('core.user_agent'))
                ->get($settings['host']);
        } catch (ConnectException $e) {
            return new UptimeResult(
                false,
                data: [
                    'message' => $e->getMessage(),
                ],
            );
        }

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
