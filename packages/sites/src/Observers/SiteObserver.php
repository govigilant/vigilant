<?php

namespace Vigilant\Sites\Observers;

use Illuminate\Support\Facades\Auth;
use Vigilant\Sites\Models\Site;
use Vigilant\Users\Models\User;

class SiteObserver
{
    public function creating(Site $site): void
    {
        /** @var User $user */
        $user = Auth::user();

        $site->team_id = $user->currentTeam->id;
    }
}
