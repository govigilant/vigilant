<?php

namespace Vigilant\Uptime\Actions;

use Illuminate\Support\Collection;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\Result;
use Vigilant\Uptime\Models\ResultAggregate;

class AggregateResults
{
    public function aggregate(Monitor $monitor): void
    {
        $results = $monitor
            ->results()
            ->select(['id', 'country'])
            ->where('created_at', '<', now()->subHour())
            ->get();

        $groupedByCountry = $results->groupBy('country');

        foreach ($groupedByCountry as $country => $countryResults) {
            $resultChunks = $countryResults->chunk(60);

            /** @var Collection $chunk */
            foreach ($resultChunks as $chunk) {
                $ids = $chunk->pluck('id');

                $query = Result::query()
                    ->whereIn('id', $ids);

                ResultAggregate::query()->create([
                    'monitor_id' => $monitor->id,
                    'total_time' => $query->average('total_time'),
                    'country' => $country,
                ]);

                $query->delete();
            }
        }
    }
}
