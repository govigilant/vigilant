<?php

namespace Vigilant\Certificates\Livewire\Monitor;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Certificates\Models\CertificateMonitor;

class Dashboard extends Component
{
    #[Locked]
    public int $monitorId;

    public function mount(int $monitorId): void
    {
        $this->monitorId = $monitorId;
    }

    public function render(): mixed
    {
        $monitor = CertificateMonitor::query()->findOrFail($this->monitorId);

        /** @var view-string $view */
        $view = 'certificates::livewire.monitor.dashboard';

        return view($view, [
            'monitor' => $monitor,
        ]);
    }
}
