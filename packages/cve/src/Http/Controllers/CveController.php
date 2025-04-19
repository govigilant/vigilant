<?php

namespace Vigilant\Cve\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Models\CveMonitor;

class CveController extends Controller
{
    public function view(CveMonitor $monitor, Cve $cve): View
    {
        return view('cve::cve', [
            'monitor' => $monitor,
            'cve' => $cve,
        ]);
    }
}
