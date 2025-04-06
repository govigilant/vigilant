<?php

namespace Vigilant\Dns\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class DnsMonitors extends Component
{
    public function render(): View
    {
        /** @var view-string $view */
        $view = 'dns::livewire.monitors';

        return view($view);
    }
}
