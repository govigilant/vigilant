<?php

namespace Vigilant\Healthchecks\Commands;

use Illuminate\Console\Command;
use Vigilant\Healthchecks\Jobs\AggregateMetricsJob;

class AggregateMetricsCommand extends Command
{
    protected $signature = 'healthchecks:aggregate-metrics';

    protected $description = 'Aggregate historical healthcheck metrics per hour';

    public function handle(): int
    {
        AggregateMetricsJob::dispatch();

        return static::SUCCESS;
    }
}
