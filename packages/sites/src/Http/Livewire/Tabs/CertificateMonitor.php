<?php

namespace Vigilant\Sites\Http\Livewire\Tabs;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Vigilant\Certificates\Models\CertificateMonitor as CertificateMonitorModel;
use Vigilant\Sites\Models\Site;

class CertificateMonitor extends Component
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
    public function monitor(): CertificateMonitorModel
    {
        /** @var Site $site */
        $site = Site::query()->findOrFail($this->siteId);

        /** @var ?CertificateMonitorModel $monitor */
        $monitor = $site->certificateMonitor;

        if ($monitor === null) {
            $monitor = new CertificateMonitorModel([
                'site_id' => $site->id,
                'domain' => preg_replace('/^https?:\/\//', '', $site->url),
                'port' => 443,
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
        /** @var view-string $view */
        $view = 'sites::livewire.tabs.certificate-monitor';

        return view($view);
    }
}
