<?php

namespace Vigilant\Uptime\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Vigilant\Uptime\Jobs\CheckUptimeJob;
use Vigilant\Uptime\Models\Monitor;

class ScheduleUptimeChecksCommand extends Command
{
    protected $signature = 'uptime:schedule';

    protected $description = 'Schedule Uptime Jobs';

    public function handle(): int
    {
        Monitor::query()
            ->withoutGlobalScopes()
            ->where('enabled', '=', true)
            ->where(function (Builder $builder): void {
                $builder->where('next_run', '<=', now())
                    ->orWhereNull('next_run');
            })
            ->get()
            ->each(function (Monitor $monitor): void {
                CheckUptimeJob::dispatch($monitor);
            });

        return static::SUCCESS;
    }
}
