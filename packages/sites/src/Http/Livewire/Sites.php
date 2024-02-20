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
        return view('sites::livewire.sites', [
            'sites' => Site::query()->paginate(5),
        ]);
    }
}
