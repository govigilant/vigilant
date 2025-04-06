<?php

namespace Vigilant\Sites\Http\Livewire\Tabs;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Sites\Models\Site;

class DnsMonitors extends Component
{
    #[Locked]
    public int $siteId;

    public function mount(Site $site): void
    {
        $this->siteId = $site->id;
    }

    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'sites::livewire.tabs.dns-monitors';

        return view($view);
    }
}
