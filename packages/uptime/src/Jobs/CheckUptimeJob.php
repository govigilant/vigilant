<?php

namespace Vigilant\Uptime\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Core\Services\TeamService;
use Vigilant\Uptime\Actions\CheckUptime;
use Vigilant\Uptime\Models\Monitor;

class CheckUptimeJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Monitor $monitor)
    {
        $this->onQueue(config('uptime.queue'));
    }

    public function handle(CheckUptime $uptime, TeamService $teamService): void
    {
        $teamService->setTeamById($this->monitor->team_id);
        $uptime->check($this->monitor);
    }

    public function uniqueId(): int
    {
        return $this->monitor->id;
    }
}
