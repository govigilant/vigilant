<?php

namespace Vigilant\Uptime\Actions;

use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Notifications\LatencyChangedNotification;

class CheckLatency
{
    public function check(Monitor $monitor): void
    {
        $currentAverage = $monitor->results()->average('total_time');

        $averages = $monitor->aggregatedResults()
            ->orderByDesc('created_at')
            ->take(12); // Past 12 hours

        // Skip if we don't have enough data
        if ($averages->count() < 10) {
            return;
        }

        $aggregatedAverage = $averages->average('total_time');

        if ($currentAverage > 0 && $aggregatedAverage > 0) {
            $percentageDifference = round((($currentAverage - $aggregatedAverage) / $aggregatedAverage) * 100);

            if (abs($percentageDifference) > 0) {
                LatencyChangedNotification::notify(
                    $monitor,
                    $percentageDifference,
                    $aggregatedAverage,
                    $currentAverage
                );
            }
        }
    }
}
