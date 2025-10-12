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
        $recentResults = $monitor->results()
            ->where('country', '=', $country)
            ->orderByDesc('created_at')
            ->take(10)
            ->pluck('total_time');

        if ($recentResults->count() < 5) {
            return;
        }

        $peakLatency = (float) $recentResults->max();
        $recentAverage = (float) $recentResults->average();

        // Check if we're experiencing a peak:
        // 1. The peak value is significantly higher than the aggregated average
        // 2. The recent average is also elevated
        if ($peakLatency > 0 && $aggregatedAverage > 0) {
            $peakPercentIncrease = (($peakLatency - $aggregatedAverage) / $aggregatedAverage) * 100;
            $recentPercentIncrease = (($recentAverage - $aggregatedAverage) / $aggregatedAverage) * 100;

            // Trigger peak notification if:
            // - Peak is at least 50% higher than average
            // - Recent average is also elevated (at least 30% higher)
            if ($peakPercentIncrease >= 50 && $recentPercentIncrease >= 30) {
                LatencyPeakNotification::notify(
                    $monitor,
                    $peakLatency,
                    $aggregatedAverage,
                    $peakPercentIncrease,
                    $country
                );
            }
        }
    }
}
