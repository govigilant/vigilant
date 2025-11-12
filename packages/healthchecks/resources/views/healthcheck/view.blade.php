<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('healthchecks.index')" :title="'Healthcheck - ' . $healthcheck->domain . (!$healthcheck->enabled ? ' (Disabled)' : '')">
            <x-frontend::page-header.actions>
                <x-form.button :href="route('healthchecks.setup', ['healthcheck' => $healthcheck])">
                    @lang('Setup')
                </x-form.button>
                <x-form.button dusk="healthcheck-edit-button" :href="route('healthchecks.edit', ['healthcheck' => $healthcheck])">
                    @lang('Edit')
                </x-form.button>
                <x-form.button class="bg-red" @click="$dispatch('open-delete-modal')">
                    @lang('Delete')
                </x-form.button>
            </x-frontend::page-header.actions>
            
            <x-frontend::page-header.mobile-actions>
                <x-form.dropdown-button :href="route('healthchecks.setup', ['healthcheck' => $healthcheck])">
                    @lang('Setup')
                </x-form.dropdown-button>
                <x-form.dropdown-button :href="route('healthchecks.edit', ['healthcheck' => $healthcheck])">
                    @lang('Edit')
                </x-form.dropdown-button>
                <x-form.dropdown-button class="!text-red hover:!text-red-light" @click="$dispatch('open-delete-modal')">
                    @lang('Delete')
                </x-form.dropdown-button>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <x-card>
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-semibold text-neutral-100">{{ __('Healthcheck Details') }}</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-neutral-400">{{ __('Domain') }}</p>
                        <p class="text-base text-neutral-100">{{ $healthcheck->domain }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-neutral-400">{{ __('Type') }}</p>
                        <p class="text-base text-neutral-100">{{ $healthcheck->type }}</p>
                    </div>
                    
                    @if($healthcheck->endpoint)
                    <div>
                        <p class="text-sm text-neutral-400">{{ __('Endpoint') }}</p>
                        <p class="text-base text-neutral-100">{{ $healthcheck->endpoint }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <p class="text-sm text-neutral-400">{{ __('Interval') }}</p>
                        <p class="text-base text-neutral-100">{{ $healthcheck->interval }}s</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-neutral-400">{{ __('Status') }}</p>
                        <p class="text-base text-neutral-100">
                            @if($healthcheck->status === 'healthy')
                                <span class="text-green-light">{{ __('Healthy') }}</span>
                            @elseif($healthcheck->status === 'unhealthy')
                                <span class="text-red">{{ __('Unhealthy') }}</span>
                            @else
                                <span class="text-neutral-400">{{ __('Unknown') }}</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-neutral-400">{{ __('Last Check') }}</p>
                        <p class="text-base text-neutral-100">
                            {{ $healthcheck->last_check_at ? $healthcheck->last_check_at->diffForHumans() : __('Never') }}
                        </p>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-data="{ showDeleteModal: false }" @open-delete-modal.window="showDeleteModal = true">
        <x-frontend::modal show="showDeleteModal">
            <x-frontend::modal.header icon="phosphor-trash" iconColor="red" show="showDeleteModal">
                @lang('Delete Healthcheck')
            </x-frontend::modal.header>

            <x-frontend::modal.body>
                <div class="space-y-4">
                    <p class="text-base-100">
                        @lang('Are you sure you want to delete this healthcheck?')
                    </p>
                    <div class="bg-base-850 border border-base-700 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                @svg('phosphor-warning-circle', 'w-5 h-5 text-orange mt-0.5')
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-base-300">
                                    <span class="font-semibold text-base-100">{{ $healthcheck->domain }}</span>
                                </p>
                                <p class="text-sm text-base-400 mt-1">
                                    @lang('This action cannot be undone. All healthcheck history and results for this monitor will be permanently deleted.')
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
                <form action="{{ route('healthchecks.delete', ['healthcheck' => $healthcheck]) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-form.button class="bg-red" type="submit">
                        @lang('Delete Healthcheck')
                    </x-form.button>
                </form>
            </x-frontend::modal.footer>
        </x-frontend::modal>
    </div>

</x-app-layout>
