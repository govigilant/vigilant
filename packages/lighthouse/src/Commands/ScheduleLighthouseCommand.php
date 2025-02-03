<?php

namespace Vigilant\Lighthouse\Commands;

use Cron\CronExpression;
use Illuminate\Console\Command;
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
            ->where('next_run', '<=', now())
            ->get()
            ->each(function (LighthouseMonitor $site) {

                if (CronExpression::isValidExpression($site->interval)) {
                    $expression = new CronExpression($site->interval);

                    if ($expression->isDue(now())) {
                        LighthouseJob::dispatch($site);
                    }
                }
            });

        return static::SUCCESS;
    }
}
