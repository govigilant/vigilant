<?php

namespace Vigilant\Notifications\Observers;

use Illuminate\Support\Facades\Auth;
use Vigilant\Notifications\Models\Trigger;
use Vigilant\Users\Models\User;

class TriggerObserver
{
    public function creating(Trigger $trigger): void
    {
        /** @var User $user */
        $user = Auth::user();

        $trigger->team_id = $user->currentTeam->id;
    }
}
