<div>
    <x-slot name="header">
        <x-page-header title="Notifications">
            <x-form.button class="bg-blue hover:bg-blue-light" :href="route('notifications.trigger.create')">
                @lang('Add Notification')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <livewire:notification-table/>
</div>
