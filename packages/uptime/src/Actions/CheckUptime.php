<?php

namespace Vigilant\Uptime\Actions;

use Vigilant\Uptime\Enums\State;
use Vigilant\Uptime\Events\DowntimeEndEvent;
use Vigilant\Uptime\Events\DowntimeStartEvent;
use Vigilant\Uptime\Events\UptimeCheckedEvent;
use Vigilant\Uptime\Models\Downtime;
use Vigilant\Uptime\Models\Monitor;

class CheckUptime
{
    public function check(Monitor $monitor): void
    {
        $monitor->update([
            'next_run' => now()->addSeconds($monitor->interval),
        ]);

        $result = $monitor->type->monitor()->process($monitor);

        /** @var ?Downtime $currentDowntime */
        $currentDowntime = $monitor->downtimes()
            ->whereNull('end')
            ->first();

        if (! $result->up) {

            if ($currentDowntime === null) {

                if ($monitor->try <= $monitor->retries) {
                    $monitor->update([
                        'try' => $monitor->try + 1,
                        'state' => State::Retrying,
                    ]);

                    return;
                }

                $monitor->downtimes()->create([
                    'start' => now(),
                    'data' => $result->data,
                ]);

                $monitor->update([
                    'state' => State::Down,
                    'try' => 0,
                ]);

                DowntimeStartEvent::dispatch($monitor);
            }

        } else {
            if ($currentDowntime !== null) {

                $currentDowntime->update([
                    'end' => now(),
                ]);

                $monitor->update([
                    'state' => State::Up,
                    'try' => 0,
                ]);

                DowntimeEndEvent::dispatch($currentDowntime);
            }

            $monitor->results()->create([
                'total_time' => $result->totalTime,
            ]);
        }

        event(new UptimeCheckedEvent($monitor));
    }
}
