<div>
    <x-slot name="header">
        <x-page-header title="Sites">
            <x-create-button dusk="site-add-button" :href="route('site.create')" model="Vigilant\Sites\Models\Site">
                @lang('Add Site')
            </x-create-button>
        </x-page-header>
    </x-slot>

    <livewire:sites.table/>

</div>
