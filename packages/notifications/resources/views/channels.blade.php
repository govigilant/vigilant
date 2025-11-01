<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Notification Channels">
            <x-create-button dusk="channel-add-button" :href="route('notifications.channel.create')" model="Vigilant\Notifications\Models\Channel">
                @lang('Add Channel')
            </x-create-button>
        </x-page-header>
    </x-slot>

    <livewire:channel-table />

</x-app-layout>
