<?php

namespace Vigilant\Uptime\Actions;

use Vigilant\Uptime\Events\DowntimeEndEvent;
use Vigilant\Uptime\Events\DowntimeStartEvent;
use Vigilant\Uptime\Models\Downtime;
use Vigilant\Uptime\Models\Monitor;

class CheckUptime
{
    public function check(Monitor $monitor): void
    {
        $result = $monitor->type->monitor()->process($monitor);

        /** @var ?Downtime $currentDowntime */
        $currentDowntime = $monitor->downtimes()
            ->whereNull('end')
            ->first();

        if (! $result->up) {

            if ($currentDowntime === null) {
                $monitor->downtimes()->create([
                    'start' => now(),
                    'data' => $result->data,
                ]);

                DowntimeStartEvent::dispatch($monitor);
            }

        } else {
            if ($currentDowntime !== null) {

                $currentDowntime->update([
                    'end' => now(),
                ]);

                DowntimeEndEvent::dispatch($currentDowntime);
            }

            $monitor->results()->create([
                'total_time' => $result->totalTime,
            ]);
        }
    }
}
