<div>
    @if (!$inline)
        <x-slot name="header">
            <x-page-header :title="$updating ? 'Edit Channel - ' . $channelModel->channel::$name : 'Add Channel'" :back="route('notifications.channels')">
                @if($updating)
                    <x-frontend::page-header.actions>
                        <x-form.button class="bg-red" @click="$dispatch('open-delete-modal')">
                            @lang('Delete')
                        </x-form.button>
                    </x-frontend::page-header.actions>
                    
                    <x-frontend::page-header.mobile-actions>
                        <x-form.dropdown-button class="!text-red hover:!text-red-light" @click="$dispatch('open-delete-modal')">
                            @lang('Delete')
                        </x-form.dropdown-button>
                    </x-frontend::page-header.mobile-actions>
                @endif
            </x-page-header>
        </x-slot>
    @endif

    <form wire:submit="save">
        <div class="max-w-7xl mx-auto">
            <x-card>
                <div class="flex flex-col gap-4">
                    @if ($testSent)
                        <x-alerts.info :title="__('Test notification sent')" />
                    @endif

                    <x-form.select field="form.channel" name="Channel" description="Choose the notification channel">
                        <option value="" disabled selected>--- Select ---</option>

                        @foreach (\Vigilant\Notifications\Facades\NotificationRegistry::channels() as $channel)
                            <option value="{{ $channel }}">{{ $channel::$name }}</option>
                        @endforeach
                    </x-form.select>

                    <h3 class="text-lg font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100">
                        {{ __('Configuration') }}</h3>

                    @if ($settingsComponent !== null)
                        @livewire($settingsComponent, ['channel' => $this->form->channel, 'settings' => $channelModel?->settings ?? []], key($this->form->channel))
                    @else
                        <span class="text-xs text-neutral-400">{{ __('Select a channel to configure') }}</span>
                    @endif

                    <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'">
                        <x-form.button wire:click="test">Test</x-form.button>
                    </x-form.submit-button>
                </div>
            </x-card>
        </div>
    </form>

    <!-- Delete Confirmation Modal -->
    @if($updating && !$inline)
        <div x-data="{ showDeleteModal: false }" @open-delete-modal.window="showDeleteModal = true">
            <x-frontend::modal show="showDeleteModal">
                <x-frontend::modal.header icon="phosphor-trash" iconColor="red" show="showDeleteModal">
                    @lang('Delete Notification Channel')
                </x-frontend::modal.header>

                <x-frontend::modal.body>
                    <div class="space-y-4">
                        <p class="text-base-100">
                            @lang('Are you sure you want to delete this notification channel?')
                        </p>
                        <div class="bg-base-850 border border-base-700 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    @svg('phosphor-warning-circle', 'w-5 h-5 text-orange mt-0.5')
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-base-300">
                                        <span class="font-semibold text-base-100">{{ $channelModel->channel::$name }}</span>
                                    </p>
                                    <p class="text-sm text-base-400 mt-1">
                                        @lang('This action cannot be undone. All channel settings will be permanently deleted and notifications will no longer be sent to this channel.')
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-frontend::modal.body>

                <x-frontend::modal.footer>
                    <x-form.button type="button" @click="showDeleteModal = false">
                        @lang('Cancel')
                    </x-form.button>
                    <x-form.button class="bg-red" type="button" wire:click="delete">
                        @lang('Delete Channel')
                    </x-form.button>
                </x-frontend::modal.footer>
            </x-frontend::modal>
        </div>
    @endif
</div>
