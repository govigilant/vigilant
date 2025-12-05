<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('healthchecks.index')" :title="'Healthcheck - ' . $healthcheck->domain . (!$healthcheck->enabled ? ' (Disabled)' : '')">
            <x-frontend::page-header.actions>
                @if ($healthcheck->type !== \Vigilant\Healthchecks\Enums\Type::Endpoint)
                    <x-form.button :href="route('healthchecks.setup', ['healthcheck' => $healthcheck])">
                        @lang('Setup')
                    </x-form.button>
                @endif
                <x-form.button dusk="healthcheck-edit-button" :href="route('healthchecks.edit', ['healthcheck' => $healthcheck])">
                    @lang('Edit')
                </x-form.button>
                <x-form.button class="bg-red" @click="$dispatch('open-delete-modal')">
                    @lang('Delete')
                </x-form.button>
            </x-frontend::page-header.actions>

            <x-frontend::page-header.mobile-actions>
                @if ($healthcheck->type !== \Vigilant\Healthchecks\Enums\Type::Endpoint)
                    <x-form.dropdown-button :href="route('healthchecks.setup', ['healthcheck' => $healthcheck])">
                        @lang('Setup')
                    </x-form.dropdown-button>
                @endif
                <x-form.dropdown-button :href="route('healthchecks.edit', ['healthcheck' => $healthcheck])">
                    @lang('Edit')
                </x-form.dropdown-button>
                <x-form.dropdown-button class="!text-red hover:!text-red-light" @click="$dispatch('open-delete-modal')">
                    @lang('Delete')
                </x-form.dropdown-button>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-frontend::stats-card :title="__('Domain')">
            {{ $healthcheck->domain }}
        </x-frontend::stats-card>

        <x-frontend::stats-card :title="__('Last Check')">
            {{ $healthcheck->last_check_at ? $healthcheck->last_check_at->diffForHumans() : __('Never') }}
        </x-frontend::stats-card>

        <x-frontend::stats-card :title="__('Status')">
            @if ($healthcheck->status === \Vigilant\Healthchecks\Enums\Status::Healthy)
                <span class="text-green-light">{{ __('Healthy') }}</span>
            @elseif($healthcheck->status === \Vigilant\Healthchecks\Enums\Status::Warning)
                <span class="text-orange">{{ __('Warning') }}</span>
            @elseif($healthcheck->status === \Vigilant\Healthchecks\Enums\Status::Unhealthy)
                <span class="text-red">{{ __('Unhealthy') }}</span>
            @else
                <span class="text-neutral-400">{{ __('Unknown') }}</span>
            @endif
        </x-frontend::stats-card>

        <x-frontend::stats-card :title="__('Interval')">
            {{ $healthcheck->interval }}s
        </x-frontend::stats-card>
    </div>

    <div class="mt-4">
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">
            {{ __('Metrics') }}
        </h2>

        <livewire:healthcheck-metric-chart :data="['healthcheckId' => $healthcheck->id]" wire:key="metric-chart" />
    </div>

    <div class="mt-4">
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">
            {{ __('Results') }}
        </h2>

        <livewire:healthcheck-result-table :healthcheckId="$healthcheck->id" wire:key="result-table" />
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
                <form action="{{ route('healthchecks.delete', ['healthcheck' => $healthcheck]) }}" method="POST"
                    class="inline">
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
