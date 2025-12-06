<?php

namespace Vigilant\Healthchecks\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Healthchecks extends Component
{
    public function render(): View
    {
        /** @var view-string $view */
        $view = 'healthchecks::livewire.healthchecks';

        return view($view);
    }
}
