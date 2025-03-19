<?php

namespace Vigilant\Dns\Http\Controllers;

use Illuminate\Routing\Controller;
use Vigilant\Dns\Models\DnsMonitor;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;

class DnsMonitorController extends Controller
{
    use DisplaysAlerts;

    public function delete(DnsMonitor $monitor): mixed
    {
        $monitor->delete();

        $this->alert(
            __('Deleted'),
            __('DNS monitor was successfully deleted'),
            AlertType::Success
        );

        return response()->redirectToRoute('dns.index');
    }
}
