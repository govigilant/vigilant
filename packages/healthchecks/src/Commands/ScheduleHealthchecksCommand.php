<?php

namespace Vigilant\Healthchecks\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Vigilant\Healthchecks\Jobs\CheckHealthcheckJob;
use Vigilant\Healthchecks\Models\Healthcheck;

class ScheduleHealthchecksCommand extends Command
{
    protected $signature = 'healthchecks:schedule';

    protected $description = 'Schedule Healthcheck Jobs';

    public function handle(): int
    {
        Healthcheck::query()
            ->withoutGlobalScopes()
            ->where('enabled', '=', true)
            ->where(function (Builder $builder): void {
                $builder->where('next_check_at', '<=', now())
                    ->orWhereNull('next_check_at');
            })
            ->get()
            ->each(function (Healthcheck $healthcheck): void {
                CheckHealthcheckJob::dispatch($healthcheck);
            });

        return static::SUCCESS;
    }
}
