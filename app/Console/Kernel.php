<?php

namespace App\Console;

use Cron\CronExpression;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Vigilant\Notifications\Commands\CreateNotificationsCommand;
use Vigilant\Uptime\Commands\AggregateResultsCommand;
use Vigilant\Uptime\Jobs\CheckUptimeJob;
use Vigilant\Uptime\Models\Monitor;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(AggregateResultsCommand::class)->hourly();

        Monitor::query()
            ->get()
            ->each(function (Monitor $monitor) use ($schedule) {
                if (CronExpression::isValidExpression($monitor->interval)) {

                    $schedule->job(new CheckUptimeJob($monitor))->cron($monitor->interval);

                }
            });


        $schedule->command(CreateNotificationsCommand::class)->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
