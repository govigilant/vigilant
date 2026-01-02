<div>
    <x-slot name="header">
        <x-page-header title="Uptime Monitoring">
            <x-frontend::page-header.actions>
                <x-create-button dusk="monitor-add-button" :href="route('uptime.monitor.create')" model="Vigilant\Uptime\Models\Monitor">
                    @lang('Add Uptime Monitor')
                </x-create-button>
            </x-frontend::page-header.actions>
            <x-frontend::page-header.mobile-actions>
                <x-create-button-dropdown :href="route('uptime.monitor.create')" model="Vigilant\Uptime\Models\Monitor">
                    @lang('Add Uptime Monitor')
                </x-create-button-dropdown>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    @if ($hasMonitors)
        <livewire:uptime-monitor-table />
    @else
        <x-uptime::empty-states.monitors />
    @endif
</div>
