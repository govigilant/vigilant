<?php

namespace Vigilant\Uptime\Http\Livewire\Monitor;

use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Uptime\Actions\CalculateUptimePercentage;
use Vigilant\Uptime\Models\Monitor;

class Dashboard extends Component
{
    #[Locked]
    public int $monitorId;

    public function mount(int $monitorId): void
    {
        $this->monitorId = $monitorId;
    }

    public function render(): View
    {
        $uptimePercentage = app(CalculateUptimePercentage::class);

        /** @var Monitor $monitor */
        $monitor = Monitor::query()->findOrFail($this->monitorId);

        /** @var view-string $view */
        $view = 'uptime::livewire.monitor.dashboard';

        return view($view, [
            'monitor' => $monitor,
            'lastDowntime' => $monitor->downtimes()
                ->whereNotNull('end')
                ->orderByDesc('start')
                ->first(),
            'uptime30d' => $uptimePercentage->calculate($monitor),
            'uptime7d' => $uptimePercentage->calculate($monitor, '-7 days'),
        ]);
    }
}
