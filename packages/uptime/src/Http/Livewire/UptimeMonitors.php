<?php

namespace Vigilant\Uptime\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Vigilant\Uptime\Models\Monitor;

class UptimeMonitors extends Component
{
    public function render(): View
    {
        /** @var view-string $view */
        $view = 'uptime::livewire.uptime-monitors';
        $hasMonitors = Monitor::query()->exists();

        return view($view, [
            'hasMonitors' => $hasMonitors,
        ]);
    }
}
