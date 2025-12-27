<?php

namespace Vigilant\Crawler\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Vigilant\Crawler\Models\Crawler as CrawlerModel;

class Crawlers extends Component
{
    public function render(): View
    {
        /** @var view-string $view */
        $view = 'crawler::crawlers';
        $hasCrawlers = CrawlerModel::query()->exists();

        return view($view, [
            'hasCrawlers' => $hasCrawlers,
        ]);
    }
}
