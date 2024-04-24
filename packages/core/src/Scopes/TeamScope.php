<?php

namespace Vigilant\Core\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Vigilant\Core\Services\TeamService;

class TeamScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        /** @var TeamService $teamService */
        $teamService = app(TeamService::class);
        $team = $teamService->team();

        abort_if($team === null, 401);

        $builder->where('team_id', '=', $team->id);
    }
}
