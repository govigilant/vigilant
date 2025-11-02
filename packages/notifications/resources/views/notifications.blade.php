<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Notifications">
            <x-create-button dusk="trigger-add-button" :href="route('notifications.trigger.create')" model="Vigilant\Notifications\Models\Trigger">
                @lang('Add Notification')
            </x-create-button>
        </x-page-header>
    </x-slot>

    <livewire:notification-table />
</x-app-layout>
