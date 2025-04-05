<?php

namespace Vigilant\Dns\Livewire\Monitor;

use Livewire\Attributes\Locked;
use Livewire\Component;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Dns\Models\DnsMonitorHistory;

class Dashboard extends Component
{
    #[Locked]
    public int $siteId;

    public function mount(int $siteId): void
    {
        $this->siteId = $siteId;
    }

    public function render(): mixed
    {
        $dnsMonitors = DnsMonitor::query()
            ->where('site_id', $this->siteId)
            ->get();

        $latestChange = DnsMonitorHistory::query()
            ->whereIn('dns_monitor_id', $dnsMonitors->pluck('id'))
            ->orderByDesc('created_at')
            ->first();

        return view('dns::livewire.monitor.dashboard', [
            'count' => $dnsMonitors->count(),
            'lastChange' => $latestChange,
        ]);
    }
}
