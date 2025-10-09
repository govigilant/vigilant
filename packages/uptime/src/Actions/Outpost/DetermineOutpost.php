<?php

namespace Vigilant\Uptime\Actions\Outpost;

use Illuminate\Database\Eloquent\Builder;
use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Jobs\UpdateMonitorLocationJob;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\Outpost;

class DetermineOutpost
{
    public function determine(?Monitor $monitor = null, array $excludedOutposts = []): ?Outpost
    {
        // If no monitor or monitor has no country, use random selection
        if ($monitor === null || $monitor->country === null) {
            if ($monitor !== null && $monitor->shouldFetchGeoip()) {
                UpdateMonitorLocationJob::dispatch($monitor);
            }

            return Outpost::query()
                ->where('status', '=', OutpostStatus::Available)
                ->when(count($excludedOutposts) > 0, fn (Builder $query) => $query->whereNotIn('id', $excludedOutposts))
                ->inRandomOrder()
                ->first();
        }

        // 50% of the time, select from the same country (closest)
        if (rand(0, 1) === 0) {
            return $this->selectSameCountryOutpost($monitor, $excludedOutposts);
        }

        // 50% of the time, select from remote countries
        return $this->selectRemoteCountryOutpost($monitor, $excludedOutposts);
    }

    protected function selectSameCountryOutpost(Monitor $monitor, array $excludedOutposts): ?Outpost
    {
        $outpost = Outpost::query()
            ->where('status', '=', OutpostStatus::Available)
            ->where('country', '=', $monitor->country)
            ->when(count($excludedOutposts) > 0, fn (Builder $query) => $query->whereNotIn('id', $excludedOutposts))
            ->inRandomOrder()
            ->first();

        // If no outpost in same country, return random from any country
        if ($outpost === null) {
            $outpost = Outpost::query()
                ->where('status', '=', OutpostStatus::Available)
                ->when(count($excludedOutposts) > 0, fn (Builder $query) => $query->whereNotIn('id', $excludedOutposts))
                ->inRandomOrder()
                ->first();
        }

        return $outpost;
    }

    protected function selectRemoteCountryOutpost(Monitor $monitor, array $excludedOutposts): ?Outpost
    {
        $outpost = Outpost::query()
            ->where('status', '=', OutpostStatus::Available)
            ->where('country', '!=', $monitor->country)
            ->when(count($excludedOutposts) > 0, fn (Builder $query) => $query->whereNotIn('id', $excludedOutposts))
            ->inRandomOrder()
            ->first();

        if ($outpost === null) {
            $outpost = Outpost::query()
                ->where('status', '=', OutpostStatus::Available)
                ->when(count($excludedOutposts) > 0, fn (Builder $query) => $query->whereNotIn('id', $excludedOutposts))
                ->inRandomOrder()
                ->first();
        }

        return $outpost;
    }
}
