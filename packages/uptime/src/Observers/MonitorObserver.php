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

        $team = $teamService->team();

        throw_if($team === null, 'No team set');

        $monitor->team_id = $team->id;
    }
}
