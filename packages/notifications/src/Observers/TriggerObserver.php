<?php

namespace Vigilant\Notifications\Observers;

use Illuminate\Support\Facades\Auth;
use Vigilant\Notifications\Models\Trigger;
use Vigilant\Users\Models\User;

class TriggerObserver
{
    public function creating(Trigger $trigger): void
    {
        if ($trigger->team_id === null) { // @phpstan-ignore-line
            /** @var ?User $user */
            $user = Auth::user();

            if ($user !== null && $user->currentTeam !== null) {
                $trigger->team_id = $user->currentTeam->id;
            }
        }
    }
}
