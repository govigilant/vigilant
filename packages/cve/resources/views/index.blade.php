<x-app-layout>
    <x-slot name="header">
        <x-page-header title="CVE Monitoring">
            <x-frontend::page-header.actions>
                <x-create-button dusk="cve-add-button" :href="route('cve.monitor.create')" model="Vigilant\Cve\Models\CveMonitor">
                    @lang('Add CVE Monitor')
                </x-create-button>
            </x-frontend::page-header.actions>
            <x-frontend::page-header.mobile-actions>
                <x-create-button-dropdown :href="route('cve.monitor.create')" model="Vigilant\Cve\Models\CveMonitor">
                    @lang('Add CVE Monitor')
                </x-create-button-dropdown>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    <livewire:cve-monitor-table />
</x-app-layout>
