<div>
    <x-slot name="header">
        <x-page-header title="Uptime Monitoring">
            <x-create-button dusk="monitor-add-button" :href="route('uptime.monitor.create')" model="Vigilant\Uptime\Models\Monitor">
                @lang('Add Uptime Monitor')
            </x-create-button>
        </x-page-header>
    </x-slot>

    <livewire:uptime-monitor-table/>
</div>
