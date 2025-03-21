<?php

namespace Vigilant\Lighthouse\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\PendingDispatch;
use Vigilant\Lighthouse\Jobs\RunLighthouseJob;
use Vigilant\Lighthouse\Models\LighthouseMonitor;

class ScheduleLighthouseCommand extends Command
{
    protected $signature = 'lighthouse:schedule';

    protected $description = 'Schedule Lighthouse Jobs';

    public function handle(): int
    {
        LighthouseMonitor::query()
            ->withoutGlobalScopes()
            ->where('enabled', '=', true)
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('run_started_at')
                    ->orWhere('run_started_at', '<=', now()->subHour());
            })
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('next_run')
                    ->orWhere('next_run', '<=', now());
            })
            ->get()
            ->each(fn (LighthouseMonitor $monitor): PendingDispatch => RunLighthouseJob::dispatch($monitor));

        return static::SUCCESS;
    }
}
