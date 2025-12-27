<?php

namespace Vigilant\Healthchecks\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Vigilant\Healthchecks\Models\Healthcheck;

class Healthchecks extends Component
{
    public function render(): View
    {
        /** @var view-string $view */
        $view = 'healthchecks::livewire.healthchecks';
        $hasHealthchecks = Healthcheck::query()->exists();

        return view($view, [
            'hasHealthchecks' => $hasHealthchecks,
        ]);
    }
}
