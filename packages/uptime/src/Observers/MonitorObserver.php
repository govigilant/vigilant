<?php

namespace Vigilant\Uptime\Observers;

use Vigilant\Core\Services\TeamService;
use Vigilant\Uptime\Models\Monitor;

class MonitorObserver
{
    public function creating(Monitor $monitor): void
    {
        /** @var TeamService $teamService */
        $teamService = app(TeamService::class);

        $monitor->team_id = $teamService->team()?->id;
    }
}
