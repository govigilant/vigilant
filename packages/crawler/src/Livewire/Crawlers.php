<?php

namespace Vigilant\Crawler\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Crawlers extends Component
{
    public function render(): View
    {
        /** @var view-string $view */
        $view = 'crawler::crawlers';

        return view($view);
    }
}
