<?php

namespace Vigilant\Lighthouse\Actions;

use Illuminate\Support\Carbon;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseSite;

class AggregateResults
{
    public function aggregate(LighthouseSite $site, Carbon $from, Carbon $till): void
    {
        $results = $site->lighthouseResults()
            ->where('aggregated', '=', false)
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $till)
            ->get();

        if ($results->isEmpty()) {
            return;
        }

        /** @var LighthouseResult $aggregate */
        $aggregate = $results->first();

        $aggregate->performance = round($results->average('performance'), 2);
        $aggregate->accessibility = round($results->average('accessibility'), 2);
        $aggregate->best_practices = round($results->average('best_practices'), 2);
        $aggregate->seo = round($results->average('seo'), 2);
        $aggregate->aggregated = true;
        $aggregate->save();

        $idsToDelete = $results
            ->where('id', '!=', $aggregate->id)
            ->pluck('id')
            ->toArray();

        LighthouseResult::query()
            ->whereIn('id', $idsToDelete)
            ->delete();
    }
}