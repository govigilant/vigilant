<?php

namespace Vigilant\Lighthouse\Livewire\Monitor;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Lighthouse\Actions\CalculateTimeDifference;
use Vigilant\Lighthouse\Models\LighthouseMonitor;

class Dashboard extends Component
{
    #[Locked]
    public int $monitorId;

    public function mount(int $monitorId): void
    {
       $this->monitorId = $monitorId;
    }

    public function render()
    {
        /** @var CalculateTimeDifference $timeDifference */
        $timeDifference = app(CalculateTimeDifference::class);

        /** @var LighthouseMonitor $monitor */
        $monitor = LighthouseMonitor::query()->findOrFail($this->monitorId);

        $lastResults = $monitor->lighthouseResults()->get();

        return view('lighthouse::livewire.monitor.dashboard', [
            'lighthouseMonitor' => $monitor,
            'lastResult' => [
                'performance' => $lastResults->average('performance'),
                'accessibility' => $lastResults->average('accessibility'),
                'best_practices' => $lastResults->average('best_practices'),
                'seo' => $lastResults->average('seo'),
            ],
            'difference' => [
                '7d' => $timeDifference->calculate($monitor, now()->subDays(7)),
                '30d' => $timeDifference->calculate($monitor, now()->subMonth()),
                '90d' => $timeDifference->calculate($monitor, now()->subMonths(3)),
                '180d' => $timeDifference->calculate($monitor, now()->subMonths(6)),
            ],
        ]);
    }
}
