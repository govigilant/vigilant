<?php

namespace Vigilant\Lighthouse\Http\Controllers;

use Illuminate\Routing\Controller;
use Vigilant\Lighthouse\Models\LighthouseSite;

class LighthouseMonitorController extends Controller
{
    public function index(LighthouseSite $lighthouseSite): mixed
    {
        return view('lighthouse::lighthouse.index', [
            'lighthouseSite' => $lighthouseSite,
            'lastResult' => $lighthouseSite->lighthouseResults()->orderByDesc('id')->first(),
        ]);
    }
}
