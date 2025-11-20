<?php

namespace Vigilant\Healthchecks\Http\Controllers;

use Illuminate\Routing\Controller;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Healthchecks\Models\Healthcheck;

class HealthcheckController extends Controller
{
    use DisplaysAlerts;

    public function index(Healthcheck $healthcheck): mixed
    {
        /** @var view-string $view */
        $view = 'healthchecks::healthcheck.view';

        return view($view, [
            'healthcheck' => $healthcheck,
        ]);
    }

    public function delete(Healthcheck $healthcheck): mixed
    {
        $healthcheck->delete();

        $this->alert(
            __('Deleted'),
            __('Healthcheck was successfully deleted'),
            AlertType::Success
        );

        return response()->redirectToRoute('healthchecks.index');
    }
}
