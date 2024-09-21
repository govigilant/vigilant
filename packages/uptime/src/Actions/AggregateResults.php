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
        $resultChunks = $monitor
            ->results()
            ->select(['id'])
            ->where('created_at', '<', now()->subHour())
            ->get()
            ->chunk(60);

        /** @var Collection $chunk */
        foreach ($resultChunks as $chunk) {
            $ids = $chunk->pluck('id');

            $query = Result::query()
                ->whereIn('id', $ids);

            ResultAggregate::query()->create([
                'monitor_id' => $monitor->id,
                'total_time' => $query->average('total_time'),
            ]);

            $query->delete();
        }
    }
}
