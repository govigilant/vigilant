<?php

namespace Vigilant\Sites\Http\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Sites extends Component
{
    public function render(): View
    {
        return view('sites::livewire.sites');
    }
}
