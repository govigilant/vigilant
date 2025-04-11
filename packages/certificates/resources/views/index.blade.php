<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Certificate Monitoring">
            <x-frontend::page-header.actions>
                <x-create-button dusk="certificate-add-button" :href="route('certificates.create')"
                    model="Vigilant\Certificates\Models\CertificateMonitor">
                    @lang('Add Certificate Monitor')
                </x-create-button>
            </x-frontend::page-header.actions>
            <x-frontend::page-header.mobile-actions>
                <x-create-button-dropdown :href="route('certificates.create')" model="Vigilant\Certificates\Models\CertificateMonitor">
                    @lang('Add Certificate Monitor')
                </x-create-button-dropdown>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    <livewire:certificate-monitor-table />
</x-app-layout>
