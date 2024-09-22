<?php

namespace Vigilant\Lighthouse\Actions;

use Illuminate\Support\Carbon;
use Vigilant\Lighthouse\Data\CategoryResultDifferenceData;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Models\LighthouseResult;

class CalculateTimeDifference
{
    public function calculate(LighthouseMonitor $monitor, Carbon $from, float $sampleSize = 0.1): ?CategoryResultDifferenceData
    {
        $results = $monitor->lighthouseResults()
            ->where('created_at', '>=', $from)
            ->get();

        if ($results->isEmpty()) {
            return null;
        }

        /** @var LighthouseResult $firstResult */
        $firstResult = $results->sortBy('created_at')->first();

        if ($firstResult->created_at === null || $from->diffInDays($firstResult->created_at) > 7) {
            return null;
        }

        $take = (int) max(1, round($results->count() * $sampleSize));

        $old = $results->take($take);
        $new = $results->skip($results->count() - $take)->take($take);

        return CategoryResultDifferenceData::of([
            'performance_old' => $old->average('performance'),
            'performance_new' => $new->average('performance'),
            'accessibility_old' => $old->average('accessibility'),
            'accessibility_new' => $new->average('accessibility'),
            'best_practices_old' => $old->average('best_practices'),
            'best_practices_new' => $new->average('best_practices'),
            'seo_old' => $old->average('seo'),
            'seo_new' => $new->average('seo'),
        ]);
    }
}
