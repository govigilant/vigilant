<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('uptime')" title="Uptime Monitor - {{ $monitor->name }}">
            <x-form.button dusk="monitor-edit-button" class="bg-blue hover:bg-blue-light"
                           :href="route('uptime.monitor.edit', ['monitor' => $monitor])">
                @lang('Edit')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <div class="grid grid-cols-3 gap-4">

        <dl class="grid grid-cols-2 gap-4">
            <div class="text-base-50 bg-base-950 text-center py-6 rounded shadow">
                <dt class="truncate text-sm font-medium text-base-100">{{ $monitor->type->label() }}</dt>
                <dd class="mt-1 text-xl font-semibold tracking-tight text-base-50">{{ $monitor->settings['host'] ?? '' }}</dd>
            </div>

            <div class="text-base-50 bg-base-950 text-center py-6 rounded shadow">
                <dt class="truncate text-sm font-medium text-base-100">@lang('Last downtime')</dt>
                <dd class="mt-1 text-xl font-semibold tracking-tight text-base-50">{{ $lastDowntime === null ? __('Never') : teamTimezone($lastDowntime->end)->diffForHumans() }}</dd>
                @if ($lastDowntime !== null)
                    <dd class="text-xs font-semibold tracking-tight text-base-50">@lang('For :time', ['time' =>  teamTimezone($lastDowntime->start)->longAbsoluteDiffForHumans(teamTimezone($lastDowntime->end))])</dd>
                @endif
            </div>

            <div class="text-base-50 bg-base-950 text-center py-6 rounded shadow">
                <dt class="truncate text-sm font-medium text-base-100">@lang('Uptime 30d')</dt>
                <dd class="mt-1 text-xl font-semibold tracking-tight text-base-50">{{ $uptime30d }}%</dd>
            </div>

            <div class="text-base-50 bg-base-950 text-center py-6 rounded shadow">
                <dt class="truncate text-sm font-medium text-base-100">@lang('Uptime 7d')</dt>
                <dd class="mt-1 text-xl font-semibold tracking-tight text-base-50">{{ $uptime7d }}%</dd>
            </div>
        </dl>

        <div class="col-span-2">
            <livewire:monitor-latency-chart :height="250" :data="['monitorId' => $monitor->id]"
                                            wire:key="latency-chart"/>
        </div>

    </div>

    <div class="mt-4">
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">{{ __('Downtimes') }}</h2>

        <livewire:uptime-downtime-table :monitorId="$monitor->id" wire:key="downtime-table"/>
    </div>

</x-app-layout>
