<?php

namespace Vigilant\Uptime\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class UptimeMonitors extends Component
{
    public function render(): View
    {
        /** @var view-string $view */
        $view = 'uptime::livewire.uptime-monitors';

        return view($view);
    }
}
