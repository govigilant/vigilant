<div>
    <x-slot name="header">
        <x-page-header title="Crawlers">
            <div class="space-x-4">
                <x-create-button dusk="crawler-add-button" class="bg-blue hover:bg-blue-light" :href="route('crawler.create')" model="Vigilant\Crawler\Models\Crawler">
                    @lang('Add Crawler')
                </x-create-button>
            </div>
        </x-page-header>
    </x-slot>

    <livewire:crawler-table/>

</div>
