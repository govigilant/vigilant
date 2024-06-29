<?php

namespace Vigilant\Lighthouse\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Vigilant\Lighthouse\Models\LighthouseResult;

class LighthouseResultController extends Controller
{
    public function index(LighthouseResult $result): View
    {
        return view('lighthouse::result.index', [
            'result' => $result,
        ]);
    }
}
