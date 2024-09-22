<?php

namespace Vigilant\Uptime\Commands;

use Illuminate\Console\Command;
use Vigilant\Core\Services\TeamService;
use Vigilant\Uptime\Actions\CheckLatency;
use Vigilant\Uptime\Models\Monitor;

class CheckLatencyCommand extends Command
{
    protected $signature = 'uptime:check-latency {monitorId}';

    protected $description = 'Check latency results';

    public function handle(CheckLatency $checkLatency, TeamService $teamService): int
    {
        /** @var int $monitorId */
        $monitorId = $this->argument('monitorId');

        /** @var Monitor $monitor */
        $monitor = Monitor::query()->withoutGlobalScopes()->findOrFail($monitorId);

        $teamService->setTeamById($monitor->team_id);

        $checkLatency->check($monitor);

        return static::SUCCESS;
    }
}
