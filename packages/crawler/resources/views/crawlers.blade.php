<div>
    <x-slot name="header">
        <x-page-header title="Crawlers">
            <x-frontend::page-header.actions>
                <x-create-button dusk="crawler-add-button" :href="route('crawler.create')"
                    model="Vigilant\Crawler\Models\Crawler">
                    @lang('Add Crawler')
                </x-create-button>
            </x-frontend::page-header.actions>
            <x-frontend::page-header.mobile-actions>
                <x-create-button-dropdown :href="route('crawler.create')" model="Vigilant\Crawler\Models\Crawler">
                    @lang('Add Crawler')
                </x-create-button-dropdown>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    <livewire:crawler-table />

</div>
