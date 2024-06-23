<?php

namespace Vigilant\Lighthouse\Http\Controllers;

use Illuminate\Routing\Controller;
use Vigilant\Lighthouse\Actions\CalculateTimeDifference;
use Vigilant\Lighthouse\Models\LighthouseSite;

class LighthouseMonitorController extends Controller
{
    public function index(LighthouseSite $lighthouseSite, CalculateTimeDifference $timeDifference): mixed
    {
        $lastResults = $lighthouseSite->lighthouseResults()
            ->where('created_at', '>=', now()->subDays(3))
            ->get();

        return view('lighthouse::lighthouse.index', [
            'lighthouseSite' => $lighthouseSite,
            'lastResult' => [
               'performance' => $lastResults->average('performance'),
               'accessibility' => $lastResults->average('accessibility'),
               'best_practices' => $lastResults->average('best_practices'),
               'seo' => $lastResults->average('seo'),
            ],
            'difference' => [
                '7d' => $timeDifference->calculate($lighthouseSite, now()->subDays(7)),
                '30d' => $timeDifference->calculate($lighthouseSite, now()->subMonth()),
                '90d' => $timeDifference->calculate($lighthouseSite, now()->subMonths(3)),
                '180d' => $timeDifference->calculate($lighthouseSite, now()->subMonths(6)),
            ],
        ]);
    }
}
