<?php

namespace Vigilant\Healthchecks\Commands;

use Illuminate\Console\Command;
use Vigilant\Healthchecks\Jobs\CheckHealthcheckJob;
use Vigilant\Healthchecks\Models\Healthcheck;

class CheckHealthcheckCommand extends Command
{
    protected $signature = 'healthchecks:check {healthcheckId}';

    protected $description = 'Check healthcheck for a specific healthcheck';

    public function handle(): int
    {
        /** @var int $healthcheckId */
        $healthcheckId = $this->argument('healthcheckId');

        /** @var Healthcheck $healthcheck */
        $healthcheck = Healthcheck::query()->withoutGlobalScopes()->findOrFail($healthcheckId);

        CheckHealthcheckJob::dispatch($healthcheck);

        return static::SUCCESS;
    }
}
