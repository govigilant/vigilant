<?php

namespace Vigilant\Lighthouse\Http\Controllers;

use Illuminate\Routing\Controller;
use Vigilant\Lighthouse\Actions\CalculateTimeDifference;
use Vigilant\Lighthouse\Models\LighthouseSite;

class LighthouseMonitorController extends Controller
{
    public function index(LighthouseSite $lighthouseSite, CalculateTimeDifference $timeDifference): mixed
    {
        return view('lighthouse::lighthouse.index', [
            'lighthouseSite' => $lighthouseSite,
            'lastResult' => $lighthouseSite->lighthouseResults()->orderByDesc('id')->first(),
            'difference' => [
                '7d' => $timeDifference->calculate($lighthouseSite, now()->subDays(7)),
                '30d' => $timeDifference->calculate($lighthouseSite, now()->subMonth()),
                '90d' => $timeDifference->calculate($lighthouseSite, now()->subMonths(3)),
            ],
        ]);
    }
}
