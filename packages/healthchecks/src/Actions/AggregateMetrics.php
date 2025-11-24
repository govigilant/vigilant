<?php

namespace Vigilant\Healthchecks\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Vigilant\Healthchecks\Models\Metric;

class AggregateMetrics
{
    public function handle(): void
    {
        $latestAggregatableHour = $this->latestAggregatableHourStart();

        if ($latestAggregatableHour === null) {
            return;
        }

        $hourBuckets = $this->hourBuckets($latestAggregatableHour);

        if ($hourBuckets->isEmpty()) {
            return;
        }

        foreach ($hourBuckets as $hourStart) {
            $this->aggregateHour($hourStart);
        }
    }

    protected function hourBuckets(Carbon $latestAggregatableHour): Collection
    {
        $hours = collect();
        $upperBound = $latestAggregatableHour->copy()->addHour();

        Metric::query()
            ->select('id', 'created_at')

            ->whereNotNull('created_at')
            ->where('created_at', '<', $upperBound)
            ->orderBy('id')
            ->chunkById(1000, function ($metrics) use (&$hours): void {
                foreach ($metrics as $metric) {
                    if ($metric->created_at === null) {
                        continue;
                    }

                    $hourStart = $metric->created_at->copy()->startOfHour();
                    $hours->put($hourStart->timestamp, $hourStart);
                }
            });

        return $hours
            ->sortKeys()
            ->values();
    }

    protected function aggregateHour(Carbon $hourStart): void
    {
        $hourEnd = $hourStart->copy()->addHour();

        $metrics = Metric::query()
            ->whereNotNull('created_at')
            ->where('created_at', '>=', $hourStart)
            ->where('created_at', '<', $hourEnd)
            ->orderBy('healthcheck_id')
            ->orderBy('key')
            ->orderBy('unit')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        if ($metrics->isEmpty()) {
            return;
        }

        $metrics
            ->groupBy(function (Metric $metric): string {
                $unit = $metric->unit ?? '__null__';

                return implode('|', [
                    $metric->healthcheck_id,
                    $metric->key,
                    $unit,
                ]);
            })
            ->each(function (Collection $group): void {
                $this->aggregateGroup($group);
            });
    }

    protected function aggregateGroup(Collection $metrics): void
    {
        if ($metrics->count() <= 1) {
            return;
        }

        $sorted = $metrics->sortBy(function (Metric $metric): string {
            $timestamp = $metric->created_at?->format('YmdHisu') ?? '000000000000000000';

            return $timestamp.'|'.$metric->id;
        })->values();

        $average = $sorted->avg(fn (Metric $metric): float => (float) $metric->value);

        if ($average === null) {
            return;
        }

        /** @var Metric $first */
        $first = $sorted->shift();

        $idsToDelete = $sorted->pluck('id')->filter()->all();

        DB::transaction(function () use ($first, $average, $idsToDelete): void {
            $first->forceFill([
                'value' => round($average, 2),
            ])->save();

            if ($idsToDelete !== []) {
                Metric::query()
                    ->whereIn('id', $idsToDelete)
                    ->delete();
            }
        });
    }

    protected function latestAggregatableHourStart(): ?Carbon
    {
        $now = Carbon::now();
        $start = $now->copy()->subHours(2)->startOfHour();

        if ($start->greaterThanOrEqualTo($now)) {
            return null;
        }

        return $start;
    }
}
