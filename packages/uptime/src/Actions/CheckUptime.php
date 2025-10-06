<?php

namespace Vigilant\Uptime\Actions;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Vigilant\Uptime\Actions\Outpost\DetermineOutpost;
use Vigilant\Uptime\Data\UptimeResult;
use Vigilant\Uptime\Enums\OutpostStatus;
use Vigilant\Uptime\Enums\State;
use Vigilant\Uptime\Events\DowntimeEndEvent;
use Vigilant\Uptime\Events\DowntimeStartEvent;
use Vigilant\Uptime\Events\UptimeCheckedEvent;
use Vigilant\Uptime\Models\Downtime;
use Vigilant\Uptime\Models\Monitor;

class CheckUptime
{
    public function __construct(
        protected DetermineOutpost $determineOutpost,
    ) {}

    public function check(Monitor $monitor): void
    {
        $monitor->update([
            'next_run' => now()->addSeconds($monitor->interval),
        ]);

        $result = null;
        $outpostCountry = null;

        for ($i = 0; $i < 3; $i++) {
            $outpost = $this->determineOutpost->determine();

            if ($outpost === null) {
                logger()->error('No outpost available for uptime check');

                continue;
            }

            try {
                $response = Http::baseUrl($outpost->url())
                    ->withToken(config('uptime.outpost_secret'))
                    ->timeout($monitor->timeout + 2) // Give some buffer time for the outpost to respond
                    ->post('run-check', [
                        'type' => $monitor->type->outpostValue(),
                        'target' => $monitor->type->formatTarget($monitor),
                        'timeout' => $monitor->timeout,
                    ]);
            } catch (ConnectionException) {
                $outpost->update([
                    'status' => OutpostStatus::Unavailable,
                ]);

                continue;
            }

            if ($response->successful()) {
                $result = new UptimeResult(
                    up: $response->json('up', false),
                    totalTime: $response->json('latency_ms', 0),
                    country: $outpost->country,
                    data: $response->json(),
                );
                $outpostCountry = $outpost->country;

                break;
            }

            $outpost->update([
                'status' => OutpostStatus::Unavailable,
            ]);
        }

        if ($result === null) {
            logger()->error('All outposts failed to perform uptime check');

            return;
        }

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
                'country' => $outpostCountry,
            ]);
        }

        event(new UptimeCheckedEvent($monitor));
    }
}
