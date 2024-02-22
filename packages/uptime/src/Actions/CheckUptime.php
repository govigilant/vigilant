<?php

namespace Vigilant\Uptime\Actions;

use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\Result;

class CheckUptime
{
    public function check(Monitor $monitor): void
    {
        $result = $monitor->type->monitor()->process($monitor);

        $monitor->results()->create([
            'total_time' => $result->totalTime,
        ]);

    }
}
