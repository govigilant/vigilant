<?php

namespace Vigilant\Users\Actions\Jetstream\Jetstream;

use Laravel\Jetstream\Contracts\DeletesTeams;
use Vigilant\Users\Models\Team;

class DeleteTeam implements DeletesTeams
{
    /**
     * Delete the given team.
     */
    public function delete(Team $team): void
    {
        $team->purge();
    }
}
