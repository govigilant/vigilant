<?php

namespace Vigilant\Cve\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Vigilant\Cve\Models\CveMonitor;

class CveMonitorController extends Controller
{
    public function view(CveMonitor $monitor): View
    {
        return view('cve::monitor', [
            'monitor' => $monitor,
        ]);
    }
}
