<?php

namespace Vigilant\Core\Services;

use Illuminate\Support\Facades\Auth;
use Vigilant\Users\Models\Team;
use Vigilant\Users\Models\User;

class TeamService
{
    protected ?Team $team = null;

    public function fromAuth(): void
    {
        /** @var ?User $user */
        $user = Auth::user();

        if ($user === null) {
            return;
        }

        /** @var User $user */
        $this->team = $user->currentTeam;
    }

    public function setTeam(?Team $team): void
    {
        $this->team = $team;
    }

    public function setTeamById(int $teamId): void
    {
        /** @var Team $team */
        $team = Team::query()->findOrFail($teamId);

        $this->setTeam($team);
    }

    public function team(): ?Team
    {
        if ($this->team === null) {
            $this->fromAuth();
        }

        return $this->team;
    }

    public static function instance(): TeamService
    {
        /** @var TeamService $instance */
        $instance = app(TeamService::class);

        return $instance;
    }

    public static function fake(): Team
    {
        /** @var Team $team */
        $team = Team::factory()->create();

        static::instance()->setTeam($team);

        return $team;
    }
}
