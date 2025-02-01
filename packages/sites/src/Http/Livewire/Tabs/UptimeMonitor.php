<?php

namespace Vigilant\Sites\Http\Livewire\Tabs;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Sites\Models\Site;
use Vigilant\Uptime\Enums\Type;
use Vigilant\Uptime\Models\Monitor;

class UptimeMonitor extends Component
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
    public function monitor(): Monitor
    {
        /** @var Site $site */
        $site = Site::query()->findOrFail($this->siteId);

        /** @var ?Monitor $monitor */
        $monitor = $site->uptimeMonitor;

        if ($monitor === null) {
            $monitor = new Monitor([
                'site_id' => $site->id,
                'name' => $site->url,
                'type' => Type::Http,
                'settings' => [
                    'host' => $site->url,
                ],
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
        return view('sites::livewire.tabs.uptime-monitor');
    }
}
