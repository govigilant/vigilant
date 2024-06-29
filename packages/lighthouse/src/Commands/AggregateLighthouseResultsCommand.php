<?php

namespace Vigilant\Lighthouse\Commands;

use Illuminate\Console\Command;
use Vigilant\Lighthouse\Jobs\AggregateLighthouseResultsJob;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseSite;

class AggregateLighthouseResultsCommand extends Command
{
    protected $signature = 'lighthouse:aggregate-results';

    protected $description = 'Aggregate Lighthouse result data';

    public function handle(): int
    {
        $sites = LighthouseSite::query()
            ->withoutGlobalScopes()
            ->get();

        /** @var LighthouseSite $site */
        foreach ($sites as $site) {
            /** @var ?LighthouseResult $lastNonAggregatedResult */
            $lastNonAggregatedResult = $site->lighthouseResults()
                ->withoutGlobalScopes()
                ->where('aggregated', '=', false)
                ->where('created_at', '<', now()->toDateString())
                ->orderBy('created_at')
                ->first();

            if ($lastNonAggregatedResult === null) {
                continue;
            }

            $days = round($lastNonAggregatedResult->created_at->diffInDays(now()));

            $start = $lastNonAggregatedResult->created_at;

            for ($i = 0; $i < $days; $i++) {

                $end = $start->clone()->addDay();

                $this->info("Aggregating for site {$site->id} from {$start->toDateString()} to {$end->toDateString()}");

                AggregateLighthouseResultsJob::dispatch($site, $start, $end);

                $start->addDay();

                if ($start->diffInDays(now()) <= 2) {
                    break;
                }
            }
        }

        return static::SUCCESS;
    }
}
