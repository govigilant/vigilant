<?php

namespace Vigilant\Notifications\Http\Livewire\Channels\Configuration;

use Illuminate\View\View;

class Slack extends ChannelConfiguration
{
    public function render(): View
    {
        /** @var view-string $view */
        $view = 'notifications::livewire.channels.configuration.slack';

        return view($view);
    }
}
