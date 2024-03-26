<?php

namespace Vigilant\Uptime\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class UptimeMonitors extends Component
{
    public function render(): View
    {
        return view('uptime::livewire.uptime-monitors');
    }
}
