<?php

namespace Vigilant\Lighthouse\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\PendingDispatch;
use Vigilant\Lighthouse\Jobs\LighthouseJob;
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
                    ->whereNull('next_run')
                    ->orWhere('next_run', '<=', now());
            })
            ->get()
            ->each(fn (LighthouseMonitor $monitor): PendingDispatch => LighthouseJob::dispatch($monitor));

        return static::SUCCESS;
    }
}
