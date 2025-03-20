<?php

namespace Vigilant\Uptime\Uptime;

use Vigilant\Uptime\Data\UptimeResult;
use Vigilant\Uptime\Models\Monitor;

class Ping extends UptimeMonitor
{
    public function process(Monitor $monitor): UptimeResult
    {
        /** @var \JJG\Ping $ping */
        $ping = app(\JJG\Ping::class, ['host' => $monitor->settings['host']]);

        $ping->setPort($monitor->settings['port']);
        $ping->setTimeout($monitor->timeout);

        for ($i = 0; $i < $monitor->retries; $i++) {
            $latency = $ping->ping();

            if ($latency) {
                break;
            }

            if ($i === $monitor->retries - 1) {
                return new UptimeResult(false, data: ['message' => 'Failed to ping host']);
            }
        }

        throw_if(! isset($latency), 'Failed to ping host');

        return new UptimeResult(true, $latency);
    }
}
