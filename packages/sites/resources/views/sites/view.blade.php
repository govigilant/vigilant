<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('sites')" title="Site - {{ $site->url }}">
            <x-form.button dusk="site-edit-button" class="bg-blue hover:bg-blue-light"
                           :href="route('site.edit', ['site' => $site])">
                @lang('Edit')
            </x-form.button>
        </x-page-header>
    </x-slot>

    @if($empty)
        <div class="text-center">
            @svg('tni-folder-plus-o', 'mx-auto h-12 w-12 text-red')

            <h3 class="mt-2 text-sm font-semibold text-white">@lang('No Monitors Configured')</h3>

            <p class="mt-1 text-sm text-base-100">
                @lang('Get started by adding monitors for this site')
            </p>
            <div class="mt-6">
                <x-form.button class="bg-blue hover:bg-blue-light" :href="route('site.edit', ['site' => $site])">
                   @lang('Configure Monitors')
                </x-form.button>
            </div>
        </div>
    @endif

    <div class="space-y-8 pb-12">


        @can('use-uptime')
            @if($uptimeMonitor !== null)
                <div>
                    <div class="mb-4">
                        <a class="text-xl text-white"
                           href="{{ route('uptime.monitor.view', ['monitor' => $site->uptimeMonitor]) }}">@lang('Uptime')</a>
                    </div>
                    <livewire:monitor-dashboard :monitorId="$uptimeMonitor->id"/>
                </div>
            @endif
        @endcan

        @if(($lighthouseMonitor = $site->lighthouseMonitors->first()) !== null)
            <div>
                <div class="mb-4">
                    <a class="text-xl text-white"
                       href="{{ route('lighthouse.index', ['monitor' => $lighthouseMonitor]) }}">@lang('Lighthouse')</a>
                </div>
                <livewire:lighthouse-monitor-dashboard :monitorId="$lighthouseMonitor->id"/>
            </div>
        @endif

        @if(($crawler = $site->crawler) !== null)
            <div>
                <div class="mb-4">
                    <a class="text-xl text-white"
                       href="{{ route('crawler.view', ['crawler' => $crawler]) }}">@lang('URL Issues')</a>
                </div>
                <livewire:crawler-dashboard :crawlerId="$crawler->id" wire:key="crawher-dashboard"/>
            </div>
        @endif

    </div>

</x-app-layout>
