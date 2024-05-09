<?php

namespace Vigilant\Uptime\Http\Controllers;

use Illuminate\Routing\Controller;
use Vigilant\Uptime\Actions\CalculateUptimePercentage;
use Vigilant\Uptime\Models\Monitor;

class UptimeMonitorController extends Controller
{
    public function index(Monitor $monitor, CalculateUptimePercentage $uptimePercentage): mixed
    {
        return view('uptime::monitor.view', [
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
