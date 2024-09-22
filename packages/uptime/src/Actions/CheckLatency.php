<?php

namespace Vigilant\Uptime\Actions;

use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Notifications\LatencyChangedNotification;

class CheckLatency
{
    public function check(Monitor $monitor): void
    {
        $currentAverage = $monitor->results()->average('total_time');

        $aggregatedAverage = $monitor->aggregatedResults()
            ->orderByDesc('created_at')
            ->take(12) // Past 12 hours
            ->average('total_time');

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
