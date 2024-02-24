<?php

namespace Vigilant\Uptime\Actions;

use Vigilant\Uptime\Models\Downtime;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\ResultAggregate;

class CalculateUptimePercentage
{
    public function calculate(Monitor $monitor): ?int
    {
        /** @var ?ResultAggregate $firstResult */
        $firstResult = $monitor->aggregatedResults()
            ->orderBy('created_at')
            ->first();

        if ($firstResult === null) {
            return null;
        }

        $minutesSinceFirstResult = now()->diffInMinutes($firstResult->created_at);

        $downtimes = $monitor->downtimes()
            ->where('created_at', '>=', $firstResult->created_at)
            ->whereNotNull('end')
            ->get();

        $downtimeMinutes = 0;

        /** @var Downtime $downtime */
        foreach($downtimes as $downtime) {

           $duration = $downtime->start->diffInMinutes($downtime->end);

           $downtimeMinutes += $duration;
        }

        $totalMinutes = $minutesSinceFirstResult - $downtimeMinutes;
        $uptimePercentage = ($totalMinutes / $minutesSinceFirstResult) * 100;

        return round($uptimePercentage, 2);
    }
}
