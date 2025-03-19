<?php

namespace Vigilant\Uptime\Http\Controllers;

use Illuminate\Routing\Controller;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Uptime\Models\Monitor;

class UptimeMonitorController extends Controller
{
    use DisplaysAlerts;

    public function index(Monitor $monitor): mixed
    {
        return view('uptime::monitor.view', [
            'monitor' => $monitor,
        ]);
    }

    public function delete(Monitor $monitor): mixed
    {
        $monitor->delete();

        $this->alert(
            __('Deleted'),
            __('Uptime monitor was successfully deleted'),
            AlertType::Success
        );

        return response()->redirectToRoute('uptime');
    }
}
