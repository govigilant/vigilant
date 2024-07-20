<div>
    <x-slot name="header">
        <x-page-header title="DNS Monitoring">
            <div class="space-x-4">
                <x-form.button dusk="dns-import-button" class="bg-green hover:bg-green-light"
                               :href="route('dns.import')">
                    @lang('Import domain')
                </x-form.button>
                <x-form.button dusk="dns-add-button" class="bg-blue hover:bg-blue-light" :href="route('dns.create')">
                    @lang('Add DNS Monitor')
                </x-form.button>
            </div>
        </x-page-header>
    </x-slot>

    <livewire:dns-monitor-table/>

</div>
