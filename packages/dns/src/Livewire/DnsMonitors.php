<?php

namespace Vigilant\Dns\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Vigilant\Dns\Models\DnsMonitor;

class DnsMonitors extends Component
{
    public function render(): View
    {
        /** @var view-string $view */
        $view = 'dns::livewire.monitors';
        $hasMonitors = DnsMonitor::query()->exists();

        return view($view, [
            'hasMonitors' => $hasMonitors,
        ]);
    }
}
