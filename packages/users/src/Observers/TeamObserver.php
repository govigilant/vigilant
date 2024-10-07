<?php

namespace Vigilant\Users\Observers;

use Illuminate\Database\Eloquent\Model;
use Vigilant\Core\Services\TeamService;

class TeamObserver
{
    public function creating(Model $model): void
    {
        /** @var TeamService $teamService */
        $teamService = app(TeamService::class);

        // @phpstan-ignore-next-line
        $model->team_id = $teamService->team()?->id;
    }
}
