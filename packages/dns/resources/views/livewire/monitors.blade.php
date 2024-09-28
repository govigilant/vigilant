<div>
    <x-slot name="header">
        <x-page-header title="DNS Monitoring">
            <div class="space-x-4">
                <x-create-button dusk="dns-import-button" :href="route('dns.import')" model="Vigilant\Dns\Models\DnsMonitor">
                    @lang('Import domain')
                </x-create-button>
                <x-create-button dusk="dns-add-button" :href="route('dns.create')" model="Vigilant\Dns\Models\DnsMonitor">
                    @lang('Add DNS Monitor')
                </x-create-button>
            </div>
        </x-page-header>
    </x-slot>

    <livewire:dns-monitor-table/>

</div>
