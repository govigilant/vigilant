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
            'certificateMonitor' => $site->certificateMonitor,
        ];

        // Define tabs configuration
        $tabs = [];
        
        if ($site->uptimeMonitor !== null) {
            $tabs[] = [
                'key' => 'uptime',
                'label' => __('Uptime'),
                'icon' => 'tni-double-caret-up-circle-o',
                'color' => 'red',
                'title' => __('Uptime Monitoring'),
                'description' => __('Monitor your site availability and response times'),
                'route' => route('uptime.monitor.view', ['monitor' => $site->uptimeMonitor]),
                'monitor' => $site->uptimeMonitor,
                'component' => 'monitor-dashboard',
                'componentKey' => 'uptime-dashboard',
                'gate' => 'use-uptime',
            ];
        }

        if ($site->lighthouseMonitors->first() !== null) {
            $tabs[] = [
                'key' => 'lighthouse',
                'label' => __('Lighthouse'),
                'icon' => 'phosphor-lighthouse-light',
                'color' => 'blue',
                'title' => __('Lighthouse Performance'),
                'description' => __('Track your site performance, accessibility, and SEO scores'),
                'route' => route('lighthouse.index', ['monitor' => $site->lighthouseMonitors->first()]),
                'monitor' => $site->lighthouseMonitors->first(),
                'component' => 'lighthouse-monitor-dashboard',
                'componentKey' => 'lighthouse-dashboard',
            ];
        }

        if ($site->crawler !== null) {
            $tabs[] = [
                'key' => 'crawler',
                'label' => __('URL Issues'),
                'icon' => 'carbon-text-link',
                'color' => 'purple',
                'title' => __('URL Issues'),
                'description' => __('Identify broken links and crawl errors on your site'),
                'route' => route('crawler.view', ['crawler' => $site->crawler]),
                'monitor' => $site->crawler,
                'component' => 'crawler-dashboard',
                'componentKey' => 'crawler-dashboard',
            ];
        }

        if ($site->dnsMonitors->count() > 0) {
            $tabs[] = [
                'key' => 'dns',
                'label' => __('DNS Records'),
                'icon' => 'phosphor-globe-simple',
                'color' => 'indigo',
                'title' => __('DNS Records'),
                'description' => __('Monitor your DNS configuration and record changes'),
                'route' => route('dns.index'),
                'monitor' => $site,
                'component' => 'dns-monitor-dashboard',
                'componentKey' => 'dns-dashboard',
            ];
        }

        if ($site->certificateMonitor !== null) {
            $tabs[] = [
                'key' => 'certificate',
                'label' => __('Certificate'),
                'icon' => 'phosphor-certificate',
                'color' => 'green',
                'title' => __('SSL Certificate'),
                'description' => __('Monitor SSL certificate validity and expiration dates'),
                'route' => route('certificates.index', ['monitor' => $site->certificateMonitor]),
                'monitor' => $site->certificateMonitor,
                'component' => 'certificate-monitor-dashboard',
                'componentKey' => 'certificate-dashboard',
                'gate' => 'use-certificates',
            ];
        }

        $data = array_merge([
            'site' => $site,
            'empty' => collect($monitors)->filter()->isEmpty(),
            'tabs' => $tabs,
        ], $monitors);

        /** @var view-string $view */
        $view = 'sites::sites.view';

        return view($view, $data);
    }
}
