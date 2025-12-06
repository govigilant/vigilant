<?php

namespace Vigilant\Uptime\Actions;

use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\Result;
use Vigilant\Uptime\Models\ResultAggregate;

class AggregateResults
{
    public function aggregate(Monitor $monitor): void
    {
        $results = $monitor
            ->results()
            ->select(['id', 'country', 'created_at'])
            ->where('created_at', '<', now()->subHour())
            ->get();

        $groupedByCountry = $results->groupBy('country');

        foreach ($groupedByCountry as $country => $countryResults) {
            /** @var \Illuminate\Support\Collection<int, Result> $countryResults */
            $groupedByHour = $countryResults->groupBy(function (Result $result) {
                return $result->created_at?->startOfHour()->toDateTimeString() ?? '';
            });

            foreach ($groupedByHour as $hour => $hourResults) {
                $ids = $hourResults->pluck('id')->toArray();

                $averageTotalTime = Result::query()
                    ->whereIn('id', $ids)
                    ->average('total_time');

                ResultAggregate::query()->create([
                    'monitor_id' => $monitor->id,
                    'total_time' => $averageTotalTime,
                    'country' => $country,
                    'created_at' => $hour,
                    'updated_at' => $hour,
                ]);

                Result::query()->whereIn('id', $ids)->delete();
            }
        }
    }
}
