<?php

namespace Vigilant\Uptime\Observers;

use Illuminate\Support\Facades\Auth;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Users\Models\User;

class MonitorObserver
{
    public function creating(Monitor $monitor): void
    {
        /** @var User $user */
        $user = Auth::user();

        $monitor->team_id = $user->currentTeam->id;
    }
}
