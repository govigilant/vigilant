<div>
    <x-slot name="header">
        <x-page-header title="Lighthouse Monitoring">
            <x-form.button dusk="lighthouse-add-button" class="bg-blue hover:bg-blue-light" :href="route('lighthouse.create')">
                @lang('Add Lighthouse Monitor')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <livewire:lighthouse-sites-table/>
</div>
