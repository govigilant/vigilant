<?php

namespace Vigilant\Lighthouse\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Vigilant\Lighthouse\Models\LighthouseResult;

class LighthouseResultController extends Controller
{
    public function index(LighthouseResult $result): View
    {
        /** @var view-string $view */
        $view = 'lighthouse::result.index';

        return view($view, [
            'result' => $result,
        ]);
    }
}
