<div>
    <x-slot name="header">
        <x-page-header title="Notification Channels">
            <x-form.button dusk="channel-add-button" class="bg-blue hover:bg-blue-light" :href="route('notifications.channel.create')">
                @lang('Add Channel')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <livewire:channel-table/>

</div>
