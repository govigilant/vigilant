<?php

namespace Vigilant\Uptime\Actions\Outpost;

use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\Outpost;

class DetermineOutpost
{
    public function determine(?Monitor $monitor = null): ?Outpost
    {
        // If no monitor or monitor has no country, use random selection
        if ($monitor === null || $monitor->country === null) {
            return Outpost::query()
                ->where('status', '=', OutpostStatus::Available)
                ->inRandomOrder()
                ->first();
        }

        // 50% of the time, select from the same country (closest)
        if (rand(0, 1) === 0) {
            return $this->selectSameCountryOutpost($monitor);
        }

        // 50% of the time, select from remote countries
        return $this->selectRemoteCountryOutpost($monitor);
    }

    protected function selectSameCountryOutpost(Monitor $monitor): ?Outpost
    {
        $outpost = Outpost::query()
            ->where('status', '=', OutpostStatus::Available)
            ->where('country', '=', $monitor->country)
            ->inRandomOrder()
            ->first();

        // If no outpost in same country, return random from any country
        if ($outpost === null) {
            $outpost = Outpost::query()
                ->where('status', '=', OutpostStatus::Available)
                ->inRandomOrder()
                ->first();
        }

        return $outpost;
    }

    protected function selectRemoteCountryOutpost(Monitor $monitor): ?Outpost
    {
        $outpost = Outpost::query()
            ->where('status', '=', OutpostStatus::Available)
            ->where('country', '!=', $monitor->country)
            ->inRandomOrder()
            ->first();

        if ($outpost === null) {
            $outpost = Outpost::query()
                ->where('status', '=', OutpostStatus::Available)
                ->inRandomOrder()
                ->first();
        }

        return $outpost;
    }
}
