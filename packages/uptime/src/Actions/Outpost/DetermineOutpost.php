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
        // If no monitor or monitor has no location, use random selection
        if ($monitor === null || $monitor->latitude === null || $monitor->longitude === null) {
            if ($monitor !== null && $monitor->shouldFetchGeoip()) {
                UpdateMonitorLocationJob::dispatch($monitor);
            }

            return Outpost::query()
                ->where('status', '=', OutpostStatus::Available)
                ->when(count($excludedOutposts) > 0, fn (Builder $query) => $query->whereNotIn('id', $excludedOutposts))
                ->inRandomOrder()
                ->first();
        }

        // Update closest outpost if not set (don't exclude from finding closest)
        if ($monitor->closest_outpost_id === null) {
            $this->updateClosestOutpost($monitor);
        }

        // 50% of the time, select the closest outpost
        if (rand(0, 1) === 0) {
            return $this->selectClosestOutpost($monitor, $excludedOutposts);
        }

        // 50% of the time, select a remote outpost
        return $this->selectRemoteOutpost($monitor, $excludedOutposts);
    }

    protected function updateClosestOutpost(Monitor $monitor): void
    {
        $closestOutpost = $this->findClosestOutpost($monitor);

        if ($closestOutpost !== null) {
            $monitor->update([
                'closest_outpost_id' => $closestOutpost->id,
            ]);
        }
    }

    protected function selectClosestOutpost(Monitor $monitor, array $excludedOutposts): ?Outpost
    {
        // Try to use the cached closest outpost if it's not excluded
        if ($monitor->closest_outpost_id !== null && ! in_array($monitor->closest_outpost_id, $excludedOutposts)) {
            $outpost = Outpost::query()
                ->where('id', $monitor->closest_outpost_id)
                ->where('status', '=', OutpostStatus::Available)
                ->first();

            if ($outpost !== null) {
                return $outpost;
            }
        }

        // Find the next closest outpost that's not excluded
        return $this->findClosestOutpost($monitor, $excludedOutposts);
    }

    protected function selectRemoteOutpost(Monitor $monitor, array $excludedOutposts): ?Outpost
    {
        $excludedIds = $excludedOutposts;
        if ($monitor->closest_outpost_id !== null) {
            $excludedIds[] = $monitor->closest_outpost_id;
        }

        $outpost = Outpost::query()
            ->where('status', '=', OutpostStatus::Available)
            ->when(count($excludedIds) > 0, fn (Builder $query) => $query->whereNotIn('id', $excludedIds))
            ->inRandomOrder()
            ->first();

        // If no remote outpost available, fallback to any available outpost
        if ($outpost === null) {
            $outpost = Outpost::query()
                ->where('status', '=', OutpostStatus::Available)
                ->when(count($excludedOutposts) > 0, fn (Builder $query) => $query->whereNotIn('id', $excludedOutposts))
                ->inRandomOrder()
                ->first();
        }

        return $outpost;
    }

    protected function findClosestOutpost(Monitor $monitor, array $excludedOutposts = []): ?Outpost
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        return Outpost::query()
            ->where('status', '=', OutpostStatus::Available)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->when(count($excludedOutposts) > 0, fn (Builder $query) => $query->whereNotIn('id', $excludedOutposts))
            ->selectRaw(
                'uptime_outposts.*, '.
                '(? * 2 * ASIN(SQRT('.
                    'POW(SIN(RADIANS((latitude - ?)) / 2), 2) + '.
                    'COS(RADIANS(?)) * COS(RADIANS(latitude)) * '.
                    'POW(SIN(RADIANS((longitude - ?)) / 2), 2)'.
                '))) as distance',
                [$earthRadius, $monitor->latitude, $monitor->latitude, $monitor->longitude]
            )
            ->orderBy('distance')
            ->first();
    }
}
