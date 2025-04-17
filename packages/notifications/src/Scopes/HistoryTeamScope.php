<?php

namespace Vigilant\Notifications\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Vigilant\Core\Services\TeamService;
use Vigilant\Notifications\Models\History;

class HistoryTeamScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        /** @var History $model */
        /** @var TeamService $teamService */
        $teamService = app(TeamService::class);
        $team = $teamService->team();

        $builder->whereHas('channel', function (Builder $query) use ($team) {
            $query->where($query->qualifyColumn('team_id'), '=', $team->id);
        });
    }
}
