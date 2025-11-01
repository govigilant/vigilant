<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('lighthouse')" :title="'Lighthouse Monitor - ' .
            $lighthouseMonitor->url .
            ($lighthouseMonitor->enabled ? '' : ' (Disabled)')">
            <x-frontend::page-header.actions>
                <x-form.button dusk="lighthouse-edit-button"
                    href="{{ route('lighthouse.edit', ['monitor' => $lighthouseMonitor]) }}">
                    @lang('Edit')
                </x-form.button>
                <x-form.button class="bg-red" @click="$dispatch('open-delete-modal')">
                    @lang('Delete')
                </x-form.button>
            </x-frontend::page-header.actions>
            
            <x-frontend::page-header.mobile-actions>
                <x-form.dropdown-button href="{{ route('lighthouse.edit', ['monitor' => $lighthouseMonitor]) }}">
                    @lang('Edit')
                </x-form.dropdown-button>
                <x-form.dropdown-button class="!text-red hover:!text-red-light" @click="$dispatch('open-delete-modal')">
                    @lang('Delete')
                </x-form.dropdown-button>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    <livewire:lighthouse-monitor-dashboard :monitorId="$lighthouseMonitor->id" />

    <div class="mt-8 grid grid-cols-1 gap-6">
        @foreach ($charts as $chart)
            <div>
                <h3 class="text-md font-bold leading-7 sm:truncate sm:text-xl sm:tracking-tight text-neutral-100">
                    {{ $chart['title'] }}</h3>
                <p class="text-sm text-neutral-400 mb-4">
                    {{ $chart['description'] }}
                    <br />
                    <a href="{{ $chart['link'] }}" target="_blank">Learn more about the {{ $chart['title'] }}
                        metric</a>.
                </p>

                <livewire:lighthouse-numeric-chart :audit="$chart['audit']" :data="['lighthouseMonitorId' => $lighthouseMonitor->id]" />
            </div>
        @endforeach
    </div>

    <div class="my-4">
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">
            {{ __('Results') }}</h2>
        <p class="text-sm text-neutral-400 mb-4">
            @lang('View the raw results from each Lighthouse run')
        </p>
        <livewire:lighthouse-results-table :monitorId="$lighthouseMonitor->id" />
    </div>

    @if (count($screenshots) > 0)
        <div class="mt-8">
            <h3 class="text-md font-bold leading-7 sm:truncate sm:text-xl sm:tracking-tight text-neutral-100">
                @lang('Timeline')</h3>

            <div class="mt-2 grid grid-cols-6 gap-4">

                @foreach ($screenshots as $screenshot)
                    <div class="text-center">
                        <img src="{{ $screenshot['data'] }}" />
                        <span class="text-xs text-neutral-200">{{ $screenshot['timing'] }}ms</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div x-data="{ showDeleteModal: false }" @open-delete-modal.window="showDeleteModal = true">
        <x-frontend::modal show="showDeleteModal">
            <x-frontend::modal.header icon="phosphor-trash" iconColor="red" show="showDeleteModal">
                @lang('Delete Lighthouse Monitor')
            </x-frontend::modal.header>

            <x-frontend::modal.body>
                <div class="space-y-4">
                    <p class="text-base-100">
                        @lang('Are you sure you want to delete this Lighthouse monitor?')
                    </p>
                    <div class="bg-base-850 border border-base-700 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                @svg('phosphor-warning-circle', 'w-5 h-5 text-orange mt-0.5')
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-base-300">
                                    <span class="font-semibold text-base-100">{{ $lighthouseMonitor->url }}</span>
                                </p>
                                <p class="text-sm text-base-400 mt-1">
                                    @lang('This action cannot be undone. All performance data and reports for this monitor will be permanently deleted.')
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
                <form action="{{ route('lighthouse.delete', ['monitor' => $lighthouseMonitor]) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-form.button class="bg-red" type="submit">
                        @lang('Delete Monitor')
                    </x-form.button>
                </form>
            </x-frontend::modal.footer>
        </x-frontend::modal>
    </div>

</x-app-layout>
