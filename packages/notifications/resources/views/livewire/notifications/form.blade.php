<div>
    <x-slot name="header">
        <x-page-header :title="$updating ? 'Edit Notification - ' . $trigger->notification::$name : 'Add Notification'" :back="route('notifications')">
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

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Main Settings Card -->
        <form wire:submit="save">
            <x-card>
                <div class="flex flex-col gap-4">
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

                    @if ($form->notification && $form->notification::info())
                        <div class="grid grid-cols-2">
                            <div></div>
                            <p class="mt-1 text-sm text-base-300 flex items-start gap-1">
                                <span class="shrink-0">ℹ️</span>
                                <span>{{ $form->notification::info() }}</span>
                            </p>
                        </div>
                    @endif

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

                    <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save Settings' : 'Create Notification'" />
                </div>
            </x-card>
        </form>

        <!-- Condition Builder Card -->
        @if ($updating)
            <form wire:submit="save">
                <x-card>
                    <div class="flex flex-col gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-base-100">@lang('Conditions')</h3>
                            <p class="text-sm text-base-400 mt-1">@lang('Only notify when these conditions match')</p>
                        </div>
                        
                        <livewire:notification-condition-builder :notification="$trigger->notification" :initial="$form->conditions" />

                        <x-form.submit-button dusk="submit-conditions-button" submitText="Save Conditions" />
                    </div>
                </x-card>
            </form>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    @if($updating)
        <div x-data="{ showDeleteModal: false }" @open-delete-modal.window="showDeleteModal = true">
            <x-frontend::modal show="showDeleteModal">
                <x-frontend::modal.header icon="phosphor-trash" iconColor="red" show="showDeleteModal">
                    @lang('Delete Notification Trigger')
                </x-frontend::modal.header>

                <x-frontend::modal.body>
                    <div class="space-y-4">
                        <p class="text-base-100">
                            @lang('Are you sure you want to delete this notification trigger?')
                        </p>
                        <div class="bg-base-850 border border-base-700 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    @svg('phosphor-warning-circle', 'w-5 h-5 text-orange mt-0.5')
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-base-300">
                                        <span class="font-semibold text-base-100">{{ $form->name }}</span>
                                    </p>
                                    <p class="text-sm text-base-400 mt-1">
                                        @lang('This action cannot be undone. All trigger conditions and settings will be permanently deleted.')
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
                        @lang('Delete Trigger')
                    </x-form.button>
                </x-frontend::modal.footer>
            </x-frontend::modal>
        </div>
    @endif
</div>
