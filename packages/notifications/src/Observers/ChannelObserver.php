<?php

namespace Vigilant\Notifications\Observers;

use Illuminate\Support\Facades\Auth;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Users\Models\User;

class ChannelObserver
{
    public function creating(Channel $channel): void
    {
        /** @var User $user */
        $user = Auth::user();

        $channel->team_id = $user->currentTeam->id;
    }
}
