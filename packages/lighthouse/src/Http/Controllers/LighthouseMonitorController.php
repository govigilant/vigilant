<?php

namespace Vigilant\Lighthouse\Http\Controllers;

use Illuminate\Routing\Controller;
use Vigilant\Lighthouse\Actions\CalculateTimeDifference;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseResultAudit;
use Vigilant\Lighthouse\Models\LighthouseSite;

class LighthouseMonitorController extends Controller
{
    public function index(LighthouseSite $lighthouseSite, CalculateTimeDifference $timeDifference): mixed
    {
        $lastResults = $lighthouseSite->lighthouseResults()
            ->get();

        /** @var ?LighthouseResult $lastResult */
        $lastResult = $lastResults->last();

        if ($lastResult !== null) {
            /** @var ?LighthouseResultAudit $screenshotAudit */
            $screenshotAudit = $lastResult->audits()
                ->firstWhere('audit', '=', 'screenshot-thumbnails');

            if ($screenshotAudit !== null) {
                $screenshots = $screenshotAudit->details['items'] ?? [];
            }
        }

        return view('lighthouse::lighthouse.index', [
            'lighthouseSite' => $lighthouseSite,
            'screenshots' => $screenshots ?? [],
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
            'charts' => [
                [
                    'audit' => 'first-contentful-paint',
                    'title' => 'First Contentful Paint',
                    'description' => ' First Contentful Paint marks the time at which the first text or image is painted.',
                    'link' => 'https://developer.chrome.com/docs/lighthouse/performance/first-contentful-paint/',
                ],
                [
                    'audit' => 'largest-contentful-paint',
                    'title' => 'Largest Contentful Paint',
                    'description' => 'Largest Contentful Paint marks the time at which the largest text or image is painted.',
                    'link' => 'https://developer.chrome.com/docs/lighthouse/performance/lighthouse-largest-contentful-paint/',
                ],
                [
                    'audit' => 'speed-index',
                    'title' => 'Speed Index',
                    'description' => ' Speed Index shows how quickly the contents of a page are visibly populated.',
                    'link' => 'https://developer.chrome.com/docs/lighthouse/performance/speed-index/',
                ],
                [
                    'audit' => 'interactive',
                    'title' => 'Time to Interactive',
                    'description' => 'Time to Interactive is the amount of time it takes for the page to become fully interactive.',
                    'link' => 'https://developer.chrome.com/docs/lighthouse/performance/interactive/',
                ],
                [
                    'audit' => 'total-blocking-time',
                    'title' => 'Total Blocking Time',
                    'description' => 'Sum of all time periods between FCP and Time to Interactive, when task length exceeded 50ms, expressed in milliseconds.',
                    'link' => 'https://developer.chrome.com/docs/lighthouse/performance/lighthouse-total-blocking-time/',
                ],
                [
                    'audit' => 'cumulative-layout-shift',
                    'title' => 'Cumulative Layout Shift',
                    'description' => 'Cumulative Layout Shift measures the movement of visible elements within the viewport.',
                    'link' => 'https://web.dev/articles/cls',
                ],

            ],
        ]);
    }
}
