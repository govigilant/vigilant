<?php

namespace Vigilant\Uptime\Commands;

use Illuminate\Console\Command;
use Vigilant\Uptime\Jobs\CheckUptimeJob;
use Vigilant\Uptime\Models\Monitor;

class CheckUptimeCommand extends Command
{
    protected $signature = 'uptime:check {monitorId}';

    protected $description = 'Check uptime for a monitor';

    public function handle(): int
    {
        /** @var int $monitorId */
        $monitorId = $this->argument('monitorId');

        /** @var Monitor $monitor */
        $monitor = Monitor::query()->findOrFail($monitorId);

        CheckUptimeJob::dispatch($monitor);

        return static::SUCCESS;
    }
}
