<?php

namespace Vigilant\Uptime\Actions;

use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Notifications\LatencyChangedNotification;
use Vigilant\Uptime\Notifications\LatencyPeakNotification;

class CheckLatency
{
    public function check(Monitor $monitor): void
    {
        $countries = $monitor->results()
            ->distinct('country')
            ->pluck('country')
            ->filter()
            ->all();

        foreach ($countries as $country) {
            $this->checkForCountry($monitor, $country);
        }
    }

    protected function checkForCountry(Monitor $monitor, string $country): void
    {
        $currentAverage = (float) $monitor->results()
            ->where('country', '=', $country)
            ->average('total_time');

        $averages = $monitor->aggregatedResults()
            ->where('country', '=', $country)
            ->orderByDesc('created_at')
            ->take(12); // Past 12 hours

        // Skip if we don't have enough data
        if ($averages->count() < 10) {
            return;
        }

        $aggregatedAverage = (float) $averages->average('total_time');

        if ($currentAverage > 0 && $aggregatedAverage > 0) {
            $percentageDifference = round((($currentAverage - $aggregatedAverage) / $aggregatedAverage) * 100);

            if (abs($percentageDifference) > 0) {
                LatencyChangedNotification::notify(
                    $monitor,
                    $percentageDifference,
                    $aggregatedAverage,
                    $currentAverage,
                    $country
                );
            }

            // Check for peak - recent results are significantly higher
            $this->checkForPeak($monitor, $country, $aggregatedAverage);
        }
    }

    protected function checkForPeak(Monitor $monitor, string $country, float $aggregatedAverage): void
    {
        // Get all recent results to calculate average
        $allRecentResults = $monitor->results()
            ->where('country', '=', $country)
            ->orderByDesc('created_at')
            ->pluck('total_time');

        if ($allRecentResults->count() < 10) {
            return;
        }

        $recentAverage = (float) $allRecentResults->average();

        // Get the last 5 checks
        $lastFiveResults = $allRecentResults->take(5);

        // Check if all of the last 5 checks are above the recent average
        $allAboveAverage = $lastFiveResults->every(function ($latency) use ($recentAverage) {
            return $latency > $recentAverage;
        });

        if ($allAboveAverage && $recentAverage > 0) {
            $peakLatency = (float) $lastFiveResults->max();
            $peakPercentIncrease = (($peakLatency - $recentAverage) / $recentAverage) * 100;

            LatencyPeakNotification::notify(
                $monitor,
                $peakLatency,
                $recentAverage,
                $peakPercentIncrease,
                $country
            );
        }
    }
}
