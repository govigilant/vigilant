<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('lighthouse')" title="Lighthouse Monitor - {{ $lighthouseMonitor->url }}">
{{--            <x-form.button dusk="lighthouse-edit-button" href="{{ route() }}" class="bg-blue hover:bg-blue-light">--}}
{{--                @lang('Edit')--}}
{{--            </x-form.button>--}}
        </x-page-header>
    </x-slot>

    <livewire:lighthouse-monitor-dashboard :monitorId="$lighthouseMonitor->id"/>

    @if (count($screenshots) > 0)
        <div class="mt-8">
            <h3 class="text-md font-bold leading-7 sm:truncate sm:text-xl sm:tracking-tight text-neutral-100">@lang('Timeline')</h3>

            <div class="mt-2 grid grid-cols-6 gap-4">

                @foreach($screenshots as $screenshot)
                    <div class="text-center">
                        <img src="{{ $screenshot['data'] }}"/>
                        <span class="text-xs text-neutral-200">{{ $screenshot['timing'] }}ms</span>
                    </div>
                @endforeach
            </div>
        </div>

    @endif

    <div class="mt-8 grid grid-cols-2 gap-12">
        @foreach($charts as $chart)
            <div>
                <h3 class="text-md font-bold leading-7 sm:truncate sm:text-xl sm:tracking-tight text-neutral-100">{{ $chart['title'] }}</h3>
                <p class="text-sm text-neutral-400 mb-4">
                    {{ $chart['description'] }}
                    <br/>
                    <a href="{{ $chart['link'] }}"
                       target="_blank">Learn more about the {{$chart['title']}} metric</a>.
                </p>

                <livewire:lighthouse-numeric-chart :audit="$chart['audit']"
                                                   :data="['monitorId' => $lighthouseMonitor->id]"/>
            </div>
        @endforeach
    </div>


    <div class="my-4">
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">{{ __('Results') }}</h2>
        <p class="text-sm text-neutral-400 mb-4">
            @lang('View the raw results from each Lighthouse run')
        </p>
        <livewire:lighthouse-results-table :monitorId="$lighthouseMonitor->id"/>
    </div>

</x-app-layout>
