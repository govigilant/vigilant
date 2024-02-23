<?php

namespace Vigilant\Uptime\Actions;

use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\ResultAggregate;

class AggregateResults
{
    public function aggregate(Monitor $monitor): void
    {
        $offset = 1;

        $allProcessed = false;

        while (! $allProcessed) {

            $resultsQuery = $monitor->results()
                ->where('created_at', '<', now()->subHours($offset))
                ->where('created_at', '>=', now()->subHours($offset + 1));

            $results = $resultsQuery->get();

            if (!$results->isEmpty()) {
                ResultAggregate::query()->create([
                    'monitor_id' => $monitor->id,
                    'total_time' => $results->average('total_time')
                ]);

                $resultsQuery->delete();
            }

            $allProcessed = $monitor->results()->count() < 2;
            $offset++;

        }
    }
}
