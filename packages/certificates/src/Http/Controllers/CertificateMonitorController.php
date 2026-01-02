<?php

namespace Vigilant\Certificates\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Vigilant\Certificates\Models\CertificateMonitor;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;

class CertificateMonitorController extends Controller
{
    use DisplaysAlerts;

    public function list(): View
    {
        /** @var view-string $view */
        $view = 'certificates::index';
        $hasMonitors = CertificateMonitor::query()->exists();

        return view($view, [
            'hasMonitors' => $hasMonitors,
        ]);
    }

    public function index(CertificateMonitor $monitor): mixed
    {
        /** @var view-string $view */
        $view = 'certificates::monitor.index';

        return view($view, [
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
