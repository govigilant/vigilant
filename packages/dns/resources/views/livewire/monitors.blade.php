<div>
    <x-slot name="header">
        <x-page-header title="DNS Monitoring">
            <x-form.button dusk="dns-add-button" class="bg-blue hover:bg-blue-light" :href="route('dns.create')">
                @lang('Add DNS Monitor')
            </x-form.button>
        </x-page-header>
    </x-slot>


</div>
