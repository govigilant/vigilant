<?php

namespace Vigilant\Lighthouse\Http\Controllers;

use Illuminate\Routing\Controller;
use Vigilant\Lighthouse\Models\LighthouseResult;

class LighthouseResultController extends Controller
{
    public function index(LighthouseResult $result)
    {
        return view('lighthouse::result.index', [
            'result' => $result
        ]);
    }
}
