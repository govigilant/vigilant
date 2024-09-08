<div>
    <x-slot name="header">
        <x-page-header title="Crawlers">
            <div class="space-x-4">
                <x-form.button dusk="crawler-add-button" class="bg-blue hover:bg-blue-light" :href="route('crawler.create')">
                    @lang('Add Crawler')
                </x-form.button>
            </div>
        </x-page-header>
    </x-slot>

    <livewire:crawler-table/>

</div>
