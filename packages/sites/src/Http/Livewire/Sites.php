<?php

namespace Vigilant\Sites\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Vigilant\Sites\Models\Site;

class Sites extends Component
{
    use WithPagination;

    public function render(): View
    {
        $sites = Site::query()
            ->with([
                'uptimeMonitor.downtimes',
                'lighthouseMonitors.lighthouseResults',
                'crawler',
                'certificateMonitor',
            ])
            ->paginate(10);

        /** @var view-string $view */
        $view = 'sites::livewire.sites';

        return view($view, [
            'sites' => $sites,
        ]);
    }
}
