<?php

namespace Vigilant\Sites\Http\Controllers;

use Illuminate\Routing\Controller;
use Vigilant\Sites\Models\Site;

class SiteController extends Controller
{
    public function view(Site $site): mixed
    {
        $monitors = [
            'uptimeMonitor' => $site->uptimeMonitor,
            'lighthouseMonitor' => $site->lighthouseMonitors->first(),
            'crawler' => $site->crawler,
        ];

        $data = array_merge([
            'site' => $site,
            'empty' => collect($monitors)->filter()->isEmpty(),
        ], $monitors);

        /** @var view-string $view */
        $view = 'sites::sites.view';

        return view($view, $data);
    }
}
