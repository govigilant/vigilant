<?php

namespace Vigilant\Uptime\Observers;

use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\Outpost;

class OutpostObserver
{
    public function updated(Outpost $outpost): void
    {
        if ($outpost->status === OutpostStatus::Unavailable) {
            Monitor::query()
                ->where('closest_outpost_id', $outpost->id)
                ->update(['closest_outpost_id' => null]);
        }
    }
}
