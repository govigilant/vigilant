<?php

namespace Vigilant\Crawler\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Crawlers extends Component
{
    public function render(): View
    {
        return view('crawler::crawlers');
    }
}
