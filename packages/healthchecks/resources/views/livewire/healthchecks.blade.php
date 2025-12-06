<div>
    <x-slot name="header">
        <x-page-header title="Healthchecks">
            <x-frontend::page-header.actions>
                <x-create-button dusk="healthcheck-add-button" :href="route('healthchecks.create')" model="Vigilant\Healthchecks\Models\Healthcheck">
                    @lang('Add Healthcheck')
                </x-create-button>
            </x-frontend::page-header.actions>
            <x-frontend::page-header.mobile-actions>
                <x-create-button-dropdown :href="route('healthchecks.create')" model="Vigilant\Healthchecks\Models\Healthcheck">
                    @lang('Add Healthcheck')
                </x-create-button-dropdown>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    <livewire:healthcheck-table />
</div>
