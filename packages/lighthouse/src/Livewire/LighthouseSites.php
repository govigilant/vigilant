<?php

namespace Vigilant\Lighthouse\Livewire;

use Livewire\Component;
use Vigilant\Lighthouse\Models\LighthouseMonitor;

class LighthouseSites extends Component
{
    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'lighthouse::livewire.lighthouse-sites';
        $hasMonitors = LighthouseMonitor::query()->exists();

        return view($view, [
            'hasMonitors' => $hasMonitors,
        ]);
    }
}
