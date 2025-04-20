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
        /** @var view-string $view */
        $view = 'cve::cve';

        return view($view, [
            'monitor' => $monitor,
            'cve' => $cve,
        ]);
    }
}
