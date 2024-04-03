<div>
    <x-slot name="header">
        <x-page-header title="Sites">
            <x-form.button class="bg-blue hover:bg-blue-light" :href="route('site.create')">
                @lang('Add Site')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <livewire:sites.table/>

</div>
