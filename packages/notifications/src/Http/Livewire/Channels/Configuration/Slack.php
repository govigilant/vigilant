<?php

namespace Vigilant\Notifications\Http\Livewire\Channels\Configuration;

use Illuminate\View\View;

class Slack extends ChannelConfiguration
{
    public function render(): View
    {
        return view('notifications::livewire.channels.configuration.slack');
    }
}
