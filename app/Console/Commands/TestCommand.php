<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Notifications\DowntimeNotification;

class TestCommand extends Command
{
    protected $signature = 'app:test';

    protected $description = 'Testing';

    public function handle()
    {
        $monitor = Monitor::query()->firstOrFail();

        DowntimeNotification::notify($monitor);
    }
}
