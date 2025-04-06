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

        /** @var view-string $view */
        $view = 'settings::tabs.team';

        return view($view, [
            'team' => $user->currentTeam,
        ]);
    }
}
