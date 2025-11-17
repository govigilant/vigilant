<?php

namespace Vigilant\Sites\Http\Livewire\Tabs;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Healthchecks\Enums\Type;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Sites\Models\Site;

class HealthcheckMonitor extends Component
{
    #[Locked]
    public int $siteId;

    public bool $enabled = false;

    public function mount(Site $site): void
    {
        $this->siteId = $site->id;
        $this->enabled = $this->healthcheck()->exists;
    }

    #[Computed]
    public function healthcheck(): Healthcheck
    {
        /** @var Site $site */
        $site = Site::query()->findOrFail($this->siteId);

        /** @var ?Healthcheck $healthcheck */
        $healthcheck = $site->healthcheck;

        if ($healthcheck === null) {
            $healthcheck = new Healthcheck([
                'site_id' => $site->id,
                'domain' => $site->url,
                'type' => Type::Endpoint,
                'enabled' => true,
                'interval' => 60,
            ]);
        }

        return $healthcheck;
    }

    #[On('save')]
    public function save(): void
    {
        if (! $this->enabled && $this->healthcheck()->exists) {
            $this->healthcheck()->delete();
        }
    }

    public function render(): mixed
    {
        /** @var view-string $view */
        $view = 'sites::livewire.tabs.healthcheck-monitor';

        return view($view);
    }
}
