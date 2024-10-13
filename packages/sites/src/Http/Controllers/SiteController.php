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

        return view('sites::sites.view', $data);
    }
}
