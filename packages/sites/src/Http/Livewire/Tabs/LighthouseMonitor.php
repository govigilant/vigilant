<?php

namespace Vigilant\Sites\Http\Livewire\Tabs;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Lighthouse\Models\LighthouseSite;
use Vigilant\Sites\Models\Site;

class LighthouseMonitor extends Component
{
    #[Locked]
    public int $siteId;

    public bool $enabled = false;

    public function mount(Site $site): void
    {
        $this->siteId = $site->id;
        $this->enabled = $this->monitor()->exists;
    }

    #[Computed]
    public function monitor(): LighthouseSite
    {
        /** @var Site $site */
        $site = Site::query()->findOrFail($this->siteId);

        /** @var ?LighthouseSite $monitor */
        $monitor = $site->lighthouseMonitors()->first();

        if ($monitor === null) {
            $monitor = new LighthouseSite([
                'site_id' => $site->id,
                'url' => $site->url,
            ]);
        }

        return $monitor;
    }

    #[On('save')]
    public function save(): void
    {
        if (! $this->enabled && $this->monitor()->exists) {
            $this->monitor()->delete();
        }
    }

    public function render(): mixed
    {
        return view('sites::livewire.tabs.lighthouse-monitor');
    }
}
