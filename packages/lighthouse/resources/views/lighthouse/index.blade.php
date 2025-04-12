<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('lighthouse')" :title="'Lighthouse Monitor - ' .
            $lighthouseMonitor->url .
            ($lighthouseMonitor->enabled ? '' : ' (Disabled)')" x-data="{ submitForm() { if (confirm('Are you sure you want to delete this monitor?')) { $refs.form.submit(); } } }">
            <form action="{{ route('lighthouse.delete', ['monitor' => $lighthouseMonitor]) }}" method="POST" wire:ignore
                x-ref="form" onsubmit="return confirm('Are you sure you want to delete this monitor?');">
                @csrf
                @method('DELETE')
            </form>

            <x-frontend::page-header.actions>
                <x-form.button dusk="lighthouse-edit-button"
                    href="{{ route('lighthouse.edit', ['monitor' => $lighthouseMonitor]) }}"
                    class="bg-blue hover:bg-blue-light">
                    @lang('Edit')
                </x-form.button>
                <x-form.button class="bg-red hover:bg-red-light" x-on:click="submitForm">
                    @lang('Delete')
                </x-form.button>
            </x-frontend::page-header.actions>
            <x-frontend::page-header.mobile-actions>
                <x-form.dropdown-button
                    href="{{ route('lighthouse.edit', ['monitor' => $lighthouseMonitor]) }}"
                    class="bg-blue hover:bg-blue-light">
                    @lang('Edit')
                </x-form.button>
                <x-form.dropdown-button class="bg-red hover:bg-red-light" x-on:click="submitForm">
                    @lang('Delete')
                </x-form.button>


            </x-frontend::page-header.mobile-actions>

        </x-page-header>
    </x-slot>

    <livewire:lighthouse-monitor-dashboard :monitorId="$lighthouseMonitor->id" />

    <div class="mt-8 grid grid-cols-2 gap-12">
        @foreach ($charts as $chart)
            <div>
                <h3 class="text-md font-bold leading-7 sm:truncate sm:text-xl sm:tracking-tight text-neutral-100">
                    {{ $chart['title'] }}</h3>
                <p class="text-sm text-neutral-400 mb-4">
                    {{ $chart['description'] }}
                    <br />
                    <a href="{{ $chart['link'] }}" target="_blank">Learn more about the {{ $chart['title'] }}
                        metric</a>.
                </p>

                <livewire:lighthouse-numeric-chart :audit="$chart['audit']" :data="['lighthouseMonitorId' => $lighthouseMonitor->id]" />
            </div>
        @endforeach
    </div>

    <div class="my-4">
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">
            {{ __('Results') }}</h2>
        <p class="text-sm text-neutral-400 mb-4">
            @lang('View the raw results from each Lighthouse run')
        </p>
        <livewire:lighthouse-results-table :monitorId="$lighthouseMonitor->id" />
    </div>

    @if (count($screenshots) > 0)
        <div class="mt-8">
            <h3 class="text-md font-bold leading-7 sm:truncate sm:text-xl sm:tracking-tight text-neutral-100">
                @lang('Timeline')</h3>

            <div class="mt-2 grid grid-cols-6 gap-4">

                @foreach ($screenshots as $screenshot)
                    <div class="text-center">
                        <img src="{{ $screenshot['data'] }}" />
                        <span class="text-xs text-neutral-200">{{ $screenshot['timing'] }}ms</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</x-app-layout>
