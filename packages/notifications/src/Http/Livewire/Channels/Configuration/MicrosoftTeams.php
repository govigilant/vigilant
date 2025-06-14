<?php

namespace Vigilant\Notifications\Http\Livewire\Channels\Configuration;

use Illuminate\View\View;

class MicrosoftTeams extends ChannelConfiguration
{
    public function render(): View
    {
        /** @var view-string $view */
        $view = 'notifications::livewire.channels.configuration.microsoft-teams';

        return view($view);
    }
}
