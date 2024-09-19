<?php

namespace Vigilant\Settings\Livewire\Tabs;

use Livewire\Component;
use Vigilant\Users\Models\User;

class Team extends Component
{
    public function render(): mixed
    {
        /** @var User $user */
        $user = auth()->user();

        return view('settings::tabs.team', [
            'team' => $user->currentTeam,
        ]);
    }
}
