<?php

namespace Vigilant\Uptime\Actions;

use Illuminate\Support\Carbon;
use Vigilant\Uptime\Models\Downtime;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\ResultAggregate;

class CalculateUptimePercentage
{
    public function calculate(Monitor $monitor, string $carbonModifier = '-30 days'): ?float
    {
        $dateThreshold = Carbon::now()->modify($carbonModifier);

        /** @var ?ResultAggregate $firstResult */
        $firstResult = $monitor->aggregatedResults()
            ->where('created_at', '>=', $dateThreshold)
            ->orderBy('created_at')
            ->first();

        if ($firstResult === null || $firstResult->created_at === null) {
            return null;
        }

        $minutesSinceFirstResult = $firstResult->created_at->diffInMinutes(now());

        $downtimes = $monitor->downtimes()
            ->where('created_at', '>=', $firstResult->created_at)
            ->whereNotNull('end')
            ->get();

        $downtimeMinutes = 0;

        /** @var Downtime $downtime */
        foreach ($downtimes as $downtime) {

            $duration = $downtime->start->diffInMinutes($downtime->end);

            $downtimeMinutes += $duration;
        }

        $totalMinutes = $minutesSinceFirstResult - $downtimeMinutes;
        $uptimePercentage = ($totalMinutes / $minutesSinceFirstResult) * 100;

        return round($uptimePercentage, 2);
    }
}
