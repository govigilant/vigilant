<?php

namespace Vigilant\Uptime\Uptime;

use Vigilant\Uptime\Data\UptimeResult;
use Vigilant\Uptime\Models\Monitor;

class Ping extends UptimeMonitor
{
    public function process(Monitor $monitor): UptimeResult
    {
        dd('ping');

        $starttime = microtime(true);
        $file      = fsockopen ($domain, 80, $errno, $errstr, 10);
        $stoptime  = microtime(true);
        $status    = 0;

        if (!$file) $status = -1;  // Site is down
        else {
            fclose($file);
            $status = ($stoptime - $starttime) * 1000;
            $status = floor($status);
        }


    }
}
