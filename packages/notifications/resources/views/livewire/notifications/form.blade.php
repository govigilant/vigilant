<div>
    <x-slot name="header">
        <x-page-header :title="$updating ? 'Edit Notification - ' . $trigger->notification::$name : 'Add Notification'" :back="route('notifications')">
        </x-page-header>
    </x-slot>

    <form wire:submit="save">
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">

            <x-form.checkbox field="form.enabled" name="Enabled" description="Enable or disable this lighthouse monitor" />

            <x-form.text field="form.name" name="Name" description="Name this notification">
            </x-form.text>

            <x-form.select field="form.notification" name="Trigger" :disabled="$updating"
                description="Choose the event that triggers this notification">
                <option value="" disabled selected>--- Select ---</option>

                @foreach (\Vigilant\Notifications\Facades\NotificationRegistry::notifications() as $notification)
                    <option value="{{ $notification }}">{{ $notification::$name }}</option>
                @endforeach
            </x-form.select>

            <x-form.number field="form.cooldown" name="Cooldown"
                description="Amount of minutes between sending notifications">
            </x-form.number>

            <x-form.checkbox field="form.all_channels" name="Sent on all channels"
                description="Send this notification to all channels">
            </x-form.checkbox>

            <x-form.select field="channels" name="Channels"
                description="Choose the channels that this notification should be sent to" multiple :disabled="$form->all_channels">
                @foreach (\Vigilant\Notifications\Models\Channel::query()->get() as $channel)
                    <option value="{{ $channel->id }}">{{ $channel->title() }}</option>
                @endforeach
            </x-form.select>

            @if ($updating)
                <h3 class="text-lg font-bold text-base-100">@lang('Only notify when these conditions match')</h3>
                <livewire:notification-condition-builder :notification="$trigger->notification" :initial="$form->conditions" />
            @endif

            <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'" />

        </div>
    </form>
</div>
