<div>
    <x-slot name="header">
        <x-page-header :title="$updating ? 'Edit Channel - ' . $channelModel->channel::$name : 'Add Channel'"
                       :back="route('notifications.channels')">
        </x-page-header>
    </x-slot>

    <form wire:submit="save">
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">
            @if($testSent)
                <x-alerts.info :title="__('Test notification sent')"/>
            @endif

            <x-form.select
                    field="form.channel"
                    name="Channel"
                    description="Choose the notification channel"
                    :disabled="$settingsComponent !== null"
            >
                <option value="" disabled selected>--- Select ---</option>

                @foreach(\Vigilant\Notifications\Facades\NotificationRegistry::channels() as $channel)
                    <option value="{{ $channel }}">{{ $channel::$name }}</option>
                @endforeach
            </x-form.select>

            <h3 class="text-lg font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100">{{ __('Configuration') }}</h3>

            @if ($settingsComponent !== null)
                @livewire($settingsComponent, ['channel' => $this->form->channel, 'settings' => $channelModel?->settings
                ?? []])
            @else
                <span class="text-xs text-neutral-400">{{ __('Select a channel to configure') }}</span>
            @endif

            <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'">
                <x-form.button wire:click="test" class="bg-blue hover:bg-blue-light">Test</x-form.button>
            </x-form.submit-button>
        </div>
    </form>
</div>
