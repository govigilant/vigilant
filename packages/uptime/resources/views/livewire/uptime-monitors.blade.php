<div>
    <x-slot name="header">
        <x-page-header title="Uptime Monitoring">
            <x-form.button class="bg-blue hover:bg-blue-light" :href="route('uptime.monitor.create')">
                @lang('Add Uptime Monitor')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <livewire:uptime-monitor-table/>
</div>
