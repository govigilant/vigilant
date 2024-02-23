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

        $latency = $ping->ping();

        if ($latency === false) {
            return new UptimeResult(false);
        }

        return new UptimeResult(true, $latency);
    }
}
