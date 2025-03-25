<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('uptime')" :title="'Uptime Monitor - ' . $monitor->name . (!$monitor->enabled ? ' (Disabled)' : '')" x-data="{ submitForm() { if (confirm('Are you sure you want to delete this monitor?')) { $refs.form.submit(); } } }">
            <form action="{{ route('uptime.monitor.delete', ['monitor' => $monitor]) }}" method="POST" x-ref="form">
                @csrf
                @method('DELETE')
            </form>
            <x-frontend::page-header.actions>
                <x-form.button class="bg-red hover:bg-red-light" x-on:click="submitForm">
                    @lang('Delete')
                </x-form.button>
                <x-form.button dusk="monitor-edit-button" class="bg-blue hover:bg-blue-light" :href="route('uptime.monitor.edit', ['monitor' => $monitor])">
                    @lang('Edit')
                </x-form.button>
            </x-frontend::page-header.actions>
            <x-frontend::page-header.mobile-actions>
                <x-form.dropdown-button :href="route('uptime.monitor.edit', ['monitor' => $monitor])">
                    @lang('Edit')
                </x-form.dropdown-button>

                <x-form.dropdown-button x-on:click="submitForm">
                    @lang('Delete')
                </x-form.dropdown-button>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    <livewire:monitor-dashboard :monitorId="$monitor->id" />

    <div class="mt-4">
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">
            {{ __('Downtimes') }}
        </h2>

        <livewire:uptime-downtime-table :monitorId="$monitor->id" wire:key="downtime-table" />
    </div>

</x-app-layout>
