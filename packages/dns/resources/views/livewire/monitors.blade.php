<div>
    <x-slot name="header">
        <x-page-header title="DNS Monitoring">
            <x-frontend::page-header.actions>
                <x-create-button dusk="dns-import-button" :href="route('dns.import')" model="Vigilant\Dns\Models\DnsMonitor">
                    @lang('Import domain')
                </x-create-button>
                <x-create-button dusk="dns-add-button" :href="route('dns.create')" model="Vigilant\Dns\Models\DnsMonitor">
                    @lang('Add DNS Monitor')
                </x-create-button>
            </x-frontend::page-header.actions>
            <x-frontend::page-header.mobile-actions>
                <x-create-button-dropdown dusk="dns-import-button" :href="route('dns.import')"
                    model="Vigilant\Dns\Models\DnsMonitor" style="dropdown">
                    @lang('Import domain')
                </x-create-button-dropdown>
                <x-create-button-dropdown dusk="dns-add-button" :href="route('dns.create')" model="Vigilant\Dns\Models\DnsMonitor"
                    style="dropdown">
                    @lang('Add DNS Monitor')
                </x-create-button-dropdown>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    <livewire:dns-monitor-table />

</div>
