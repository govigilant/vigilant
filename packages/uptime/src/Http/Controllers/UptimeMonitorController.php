<?php

namespace Vigilant\Uptime\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Vigilant\Uptime\Models\Monitor;

class UptimeMonitorController extends Controller
{
    public function index(Monitor $monitor): View
    {
        return view('uptime::monitor.view', [
            'monitor' => $monitor,
        ]);
    }
}
