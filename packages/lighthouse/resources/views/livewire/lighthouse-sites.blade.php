<div>
    <x-slot name="header">
        <x-page-header title="Lighthouse Monitoring">
            <x-frontend::page-header.actions>
                <x-create-button dusk="lighthouse-add-button" :href="route('lighthouse.create')"
                    model="Vigilant\Lighthouse\Models\LighthouseMonitor">
                    @lang('Add Lighthouse Monitor')
                </x-create-button>
            </x-frontend::page-header.actions>
            <x-frontend::page-header.mobile-actions>
                <x-create-button-dropdown :href="route('lighthouse.create')" model="Vigilant\Lighthouse\Models\LighthouseMonitor">
                    @lang('Add Lighthouse Monitor')
                </x-create-button-dropdown>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    @if ($hasMonitors)
        <livewire:lighthouse-sites-table />
    @else
        <x-lighthouse::empty-states.monitors />
    @endif
</div>
