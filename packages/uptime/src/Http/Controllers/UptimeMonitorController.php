<?php

namespace Vigilant\Uptime\Http\Controllers;

use Illuminate\Routing\Controller;
use Vigilant\Uptime\Models\Monitor;

class UptimeMonitorController extends Controller
{
    public function index(Monitor $monitor)
    {
       return view('uptime::monitor.view', [
           'monitor' => $monitor,
       ]);
    }
}
