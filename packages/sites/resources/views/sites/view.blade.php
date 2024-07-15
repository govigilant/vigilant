<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('sites')" title="Site - {{ $site->url }}">
            <x-form.button dusk="site-edit-button" class="bg-blue hover:bg-blue-light"
                           :href="route('site.edit', ['site' => $site])">
                @lang('Edit')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <div class="space-y-8">
        @if(($uptimeMonitor = $site->uptimeMonitor) !== null)
            <div>
                <div class="mb-4">
                    <a class="text-xl text-white"
                       href="{{ route('uptime.monitor.view', ['monitor' => $site->uptimeMonitor]) }}">@lang('Uptime')</a>
                </div>
                <livewire:monitor-dashboard :monitorId="$uptimeMonitor->id"/>
            </div>
        @endif

        @if(($lighthouseMonitor = $site->lighthouseMonitors->first()) !== null)
            <div>
                <div class="mb-4">
                    <a class="text-xl text-white"
                       href="{{ route('lighthouse.index', ['monitor' => $lighthouseMonitor]) }}">@lang('Lighthouse')</a>
                </div>
                <livewire:lighthouse-monitor-dashboard :monitorId="$lighthouseMonitor->id"/>
            </div>
        @endif
    </div>

</x-app-layout>
