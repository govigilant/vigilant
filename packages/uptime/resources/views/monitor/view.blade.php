<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('uptime')" :title="'Uptime Monitor - '. $monitor->name . (!$monitor->enabled ? ' (Disabled)' : '')">
            <x-form.button dusk="monitor-edit-button" class="bg-blue hover:bg-blue-light"
                           :href="route('uptime.monitor.edit', ['monitor' => $monitor])">
                @lang('Edit')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <livewire:monitor-dashboard :monitorId="$monitor->id"/>

    <div class="mt-4">
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">{{ __('Downtimes') }}</h2>

        <livewire:uptime-downtime-table :monitorId="$monitor->id" wire:key="downtime-table"/>
    </div>

</x-app-layout>
