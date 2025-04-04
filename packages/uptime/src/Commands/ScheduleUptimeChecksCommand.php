<?php

namespace Vigilant\Uptime\Commands;

use Cron\CronExpression;
use Illuminate\Console\Command;
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
            ->get()
            ->each(function (Monitor $monitor) {
                if (CronExpression::isValidExpression($monitor->interval)) {
                    $expression = new CronExpression($monitor->interval);

                    if ($expression->isDue(now())) {
                        CheckUptimeJob::dispatch($monitor);
                    }
                }
            });

        return static::SUCCESS;
    }
}
