<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Vigilant\Certificates\Commands\CheckCertificatesCommand;
use Vigilant\Crawler\Commands\CrawlUrlsCommand;
use Vigilant\Crawler\Commands\ProcessCrawlerStatesCommand;
use Vigilant\Crawler\Commands\ScheduleCrawlersCommand;
use Vigilant\Cve\Commands\ImportCvesCommand;
use Vigilant\Dns\Commands\CheckAllDnsRecordsCommand;
use Vigilant\Lighthouse\Commands\AggregateLighthouseResultsCommand;
use Vigilant\Lighthouse\Commands\ScheduleLighthouseCommand;
use Vigilant\Notifications\Commands\CreateNotificationsCommand;
use Vigilant\Uptime\Commands\AggregateResultsCommand;
use Vigilant\Uptime\Commands\ScheduleUptimeChecksCommand;
use Vigilant\Uptime\Commands\CheckUnavailableOutpostsCommand;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Uptime
        $schedule->command(AggregateResultsCommand::class)->hourly();
        $schedule->command(ScheduleUptimeChecksCommand::class)->everySecond();
        $schedule->command(CheckUnavailableOutpostsCommand::class)->everyFifteenMinutes();

        // Lighthouse
        $schedule->command(ScheduleLighthouseCommand::class)->everySecond();
        $schedule->command(AggregateLighthouseResultsCommand::class)->daily();

        // Dns
        $schedule->command(CheckAllDnsRecordsCommand::class)->hourly();

        // Crawler
        $schedule->command(ScheduleCrawlersCommand::class)->everyMinute();
        $schedule->command(CrawlUrlsCommand::class)->everyMinute();
        $schedule->command(ProcessCrawlerStatesCommand::class)->everyMinute();

        // Certificates
        $schedule->command(CheckCertificatesCommand::class)->everyMinute();

        // CVE
        $schedule->command(ImportCvesCommand::class, ['now - 1 hour'])->everyThirtyMinutes();

        // Notifications
        $schedule->command(CreateNotificationsCommand::class)->daily();

        $schedule->command('model:prune', [
            '--model' => array_keys(config('core.data_retention')),
        ])->hourly();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
