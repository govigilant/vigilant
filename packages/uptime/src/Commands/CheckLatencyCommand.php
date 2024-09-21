<?php

namespace Vigilant\Uptime\Commands;

use Illuminate\Console\Command;
use Vigilant\Uptime\Actions\CheckLatency;
use Vigilant\Uptime\Models\Monitor;

class CheckLatencyCommand extends Command
{
    protected $signature = 'uptime:check-latency {monitorId}';

    protected $description = 'Check latency results';

    public function handle(CheckLatency $checkLatency): int
    {
        /** @var int $monitorId */
        $monitorId = $this->argument('monitorId');

        /** @var Monitor $monitor */
        $monitor = Monitor::query()->withoutGlobalScopes()->findOrFail($monitorId);

        $checkLatency->check($monitor);

        return static::SUCCESS;
    }
}
