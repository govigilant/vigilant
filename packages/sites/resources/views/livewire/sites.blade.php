<div>
    <x-slot name="header">
        <x-page-header title="Sites">
            <x-frontend::page-header.actions>
                <x-create-button dusk="site-import-button" :href="route('site.import')" model="Vigilant\Sites\Models\Site">
                    @lang('Add Multiple Sites')
                </x-create-button>
                <x-create-button dusk="site-add-button" :href="route('site.create')" model="Vigilant\Sites\Models\Site">
                    @lang('Add Site')
                </x-create-button>

            </x-frontend::page-header.actions>
            <x-frontend::page-header.mobile-actions>
                <x-create-button-dropdown :href="route('site.create')" model="Vigilant\Sites\Models\Site">
                    @lang('Add Site')
                </x-create-button-dropdown>
                <x-create-button-dropdown :href="route('site.import')" model="Vigilant\Sites\Models\Site">
                    @lang('Add Multiple Sites')
                </x-create-button-dropdown>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    <livewire:sites.table />

</div>
