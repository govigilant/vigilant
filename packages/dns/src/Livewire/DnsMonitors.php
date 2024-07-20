<?php

namespace Vigilant\Dns\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class DnsMonitors extends Component
{
    public function render(): View
    {
       return view('dns::livewire.monitors');
    }
}
