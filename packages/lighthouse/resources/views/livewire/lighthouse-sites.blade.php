<div>
    <x-slot name="header">
        <x-page-header title="Lighthouse Monitoring">
            <x-create-button dusk="lighthouse-add-button" :href="route('lighthouse.create')" model="Vigilant\Lighthouse\Models\LighthouseMonitor">
                @lang('Add Lighthouse Monitor')
            </x-create-button>
        </x-page-header>
    </x-slot>

    <livewire:lighthouse-sites-table/>
</div>
