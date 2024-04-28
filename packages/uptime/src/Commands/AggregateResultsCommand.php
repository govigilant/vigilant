<?php

namespace Vigilant\Uptime\Commands;

use Illuminate\Console\Command;
use Vigilant\Uptime\Jobs\AggregateResultsJob;
use Vigilant\Uptime\Models\Monitor;

class AggregateResultsCommand extends Command
{
    protected $signature = 'uptime:aggregate-results';

    protected $description = 'Aggregate the results of the uptime checks';

    public function handle(): int
    {
        $monitors = Monitor::query()
            ->withoutGlobalScopes()
            ->get();

        foreach ($monitors as $monitor) {
            AggregateResultsJob::dispatch($monitor);
        }

        return static::SUCCESS;
    }
}
