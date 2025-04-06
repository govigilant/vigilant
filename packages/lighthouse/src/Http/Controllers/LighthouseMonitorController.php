<?php

namespace Vigilant\Lighthouse\Http\Controllers;

use Illuminate\Routing\Controller;
use Vigilant\Frontend\Concerns\DisplaysAlerts;
use Vigilant\Frontend\Enums\AlertType;
use Vigilant\Lighthouse\Models\LighthouseMonitor;
use Vigilant\Lighthouse\Models\LighthouseResult;
use Vigilant\Lighthouse\Models\LighthouseResultAudit;

class LighthouseMonitorController extends Controller
{
    use DisplaysAlerts;

    public function index(LighthouseMonitor $monitor): mixed
    {
        $lastResults = $monitor->lighthouseResults()->get();

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

        /** @var view-string $view */
        $view = 'lighthouse::lighthouse.index';

        return view($view, [
            'lighthouseMonitor' => $monitor,
            'screenshots' => $screenshots ?? [],
            'charts' => [
                [
                    'audit' => 'first-contentful-paint',
                    'title' => 'First Contentful Paint',
                    'description' => 'First Contentful Paint marks the time at which the first text or image is painted.',
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

    public function delete(LighthouseMonitor $monitor): mixed
    {
        $monitor->delete();

        $this->alert(
            __('Deleted'),
            __('Lighthouse monitor was successfully deleted'),
            AlertType::Success
        );

        return response()->redirectToRoute('lighthouse');
    }
}
