<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Vigilant\Dns\Commands\CheckAllDnsRecordsCommand;
use Vigilant\Lighthouse\Commands\AggregateLighthouseResultsCommand;
use Vigilant\Lighthouse\Commands\ScheduleLighthouseCommand;
use Vigilant\Notifications\Commands\CreateNotificationsCommand;
use Vigilant\Uptime\Commands\AggregateResultsCommand;
use Vigilant\Uptime\Commands\ScheduleUptimeChecksCommand;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Uptime
        $schedule->command(AggregateResultsCommand::class)->hourly();
        $schedule->command(ScheduleUptimeChecksCommand::class)->everyMinute();

        // Lighthouse
        $schedule->command(ScheduleLighthouseCommand::class)->everyMinute();
        $schedule->command(AggregateLighthouseResultsCommand::class)->daily();

        // Dns
        $schedule->command(CheckAllDnsRecordsCommand::class)->hourly();

        // Notifications
        $schedule->command(CreateNotificationsCommand::class)->daily();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
