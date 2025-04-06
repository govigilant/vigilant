<?php

namespace Vigilant\Dns\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Vigilant\Dns\Models\DnsMonitor;

class DnsMonitorHistory extends Component
{
    public DnsMonitor $monitor;

    public function mount(DnsMonitor $monitor): void
    {
        $this->monitor = $monitor;
    }

    public function render(): View
    {
        /** @var view-string $view */
        $view = 'dns::livewire.monitor-history';

        return view($view, [
            'monitor' => $this->monitor,
        ]);
    }
}
