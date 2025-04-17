<?php

namespace Vigilant\Lighthouse\Livewire;

use Livewire\Component;

class LighthouseSites extends Component
{
    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'lighthouse::livewire.lighthouse-sites';

        return view($view);
    }
}
