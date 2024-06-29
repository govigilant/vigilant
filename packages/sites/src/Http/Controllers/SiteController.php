<?php

namespace Vigilant\Sites\Http\Controllers;

use Illuminate\Routing\Controller;
use Vigilant\Sites\Models\Site;

class SiteController extends Controller
{
    public function view(Site $site): mixed
    {
        return view('sites::sites.view', [
            'site' => $site,
        ]);
    }
}
