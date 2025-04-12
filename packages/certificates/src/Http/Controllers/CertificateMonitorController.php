<?php

namespace Vigilant\Certificates\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Vigilant\Certificates\Models\CertificateMonitor;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;

class CertificateMonitorController extends Controller
{
    use DisplaysAlerts;

    public function index(CertificateMonitor $monitor): mixed
    {
        return view('certificates::monitor.index', [
            'monitor' => $monitor,
        ]);
    }

    public function delete(CertificateMonitor $monitor): RedirectResponse
    {
        $monitor->delete();

        $this->alert(
            __('Deleted'),
            __('Certificate monitor was successfully deleted'),
            AlertType::Success
        );

        return response()->redirectToRoute('certificates');
    }
}
