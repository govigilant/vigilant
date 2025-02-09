<?php

namespace Vigilant\Dns\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Vigilant\Dns\Models\DnsMonitor;

class DnsMonitorHistory extends Component
{
    public DnsMonitor $monitor;

    public function mount(DnsMonitor $monitor)
    {
        $this->monitor = $monitor;
    }

    public function render(): View
    {
        return view('dns::livewire.monitor-history', [
            'monitor' => $this->monitor,
        ]);
    }
}
