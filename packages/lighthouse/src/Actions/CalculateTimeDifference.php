<?php

namespace Vigilant\Lighthouse\Actions;

use Illuminate\Support\Carbon;
use Vigilant\Lighthouse\Data\CategoryResultDifferenceData;
use Vigilant\Lighthouse\Models\LighthouseSite;

class CalculateTimeDifference
{
    public function calculate(LighthouseSite $site, Carbon $from, float $sampleSize = 0.1): ?CategoryResultDifferenceData
    {
        $results = $site->lighthouseResults()
            ->where('created_at', '>=', $from)
            ->get();

        if ($results->isEmpty()) {
            return null;
        }

        $take = max(1, round($results->count() * $sampleSize));

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
