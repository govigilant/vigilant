<?php

namespace Vigilant\Uptime\Observers;

use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\Outpost;

class OutpostObserver
{
    public function updated(Outpost $outpost): void
    {
        // If outpost becomes unavailable, clear it from monitors using it as closest outpost
        if ($outpost->status === OutpostStatus::Unavailable) {
            Monitor::query()
                ->withoutGlobalScopes()
                ->where('closest_outpost_id', $outpost->id)
                ->update(['closest_outpost_id' => null]);

            // Set unavailable_at if not already set
            if ($outpost->unavailable_at === null) {
                $outpost->updateQuietly(['unavailable_at' => now()]);
            }
        }
    }

    public function deleted(Outpost $outpost): void
    {
        // Clear the closest_outpost_id for monitors using this outpost
        Monitor::query()
            ->withoutGlobalScopes()
            ->where('closest_outpost_id', $outpost->id)
            ->update(['closest_outpost_id' => null]);
    }
}
