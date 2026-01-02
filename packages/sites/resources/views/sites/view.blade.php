<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('sites')" title="Site - {{ $site->url }}">
            <x-frontend::page-header.actions>
                <x-form.button dusk="site-edit-button" :href="route('site.edit', ['site' => $site])">
                    @lang('Edit')
                </x-form.button>
                <x-form.button class="bg-red" @click="$dispatch('open-delete-modal')">
                    @lang('Delete')
                </x-form.button>
            </x-frontend::page-header.actions>

            <x-frontend::page-header.mobile-actions>
                <x-form.dropdown-button dusk="site-edit-button" :href="route('site.edit', ['site' => $site])">
                    @lang('Edit')
                </x-form.dropdown-button>
                <x-form.dropdown-button class="!text-red hover:!text-red-light" @click="$dispatch('open-delete-modal')">
                    @lang('Delete')
                </x-form.dropdown-button>
            </x-frontend::page-header.mobile-actions>
        </x-page-header>
    </x-slot>

    @if ($empty)
        <x-sites::empty-states.no-monitors :site="$site" />
    @else
        <x-frontend::tabs.container :activeTab="!empty($tabs) ? $tabs[0]['key'] : ''" class="pb-12">
            
            {{-- Tab Navigation --}}
            <x-frontend::tabs.navigation :tabs="$tabs" />

            {{-- Tab Panels --}}
            <div class="space-y-6">
                @foreach($tabs as $index => $tab)
                    @if(!isset($tab['gate']) || auth()->user()->can($tab['gate']))
                        <x-frontend::tabs.panel :key="$tab['key']" :cloak="$index !== 0">
                            
                            {{-- Section Header with Link --}}
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-2xl font-bold text-base-50 flex items-center gap-3">
                                        @svg($tab['icon'], 'size-6 text-' . $tab['color'])
                                        {{ $tab['title'] }}
                                    </h2>
                                    <p class="text-base-300 mt-1">{{ $tab['description'] }}</p>
                                </div>
                                <a href="{{ $tab['route'] }}" 
                                   class="group flex items-center gap-2 px-4 py-2.5 rounded-lg border-2 border-base-700 bg-base-850/50 hover:border-{{ $tab['color'] }} hover:bg-base-800/50 text-base-200 hover:text-base-50 transition-all duration-300 text-sm font-medium">
                                    <span>@lang('View Details')</span>
                                    @svg('tni-right-o', 'size-4 group-hover:translate-x-1 transition-transform duration-300')
                                </a>
                            </div>

                            <x-frontend::card>
                                @if($tab['key'] === 'uptime')
                                    <livewire:monitor-dashboard :monitorId="$tab['monitor']->id" />
                                @elseif($tab['key'] === 'lighthouse')
                                    <livewire:lighthouse-monitor-dashboard :monitorId="$tab['monitor']->id" />
                                @elseif($tab['key'] === 'crawler')
                                    <livewire:crawler-dashboard :crawlerId="$tab['monitor']->id" wire:key="{{ $tab['componentKey'] }}" />
                                @elseif($tab['key'] === 'dns')
                                    <livewire:dns-monitor-dashboard :siteId="$tab['monitor']->id" wire:key="{{ $tab['componentKey'] }}" />
                                @elseif($tab['key'] === 'certificate')
                                    <livewire:certificate-monitor-dashboard :monitorId="$tab['monitor']->id" />
                                @elseif($tab['key'] === 'healthcheck')
                                    <livewire:healthcheck-dashboard :healthcheckId="$tab['monitor']->id" wire:key="{{ $tab['componentKey'] }}" />
                                @endif
                            </x-frontend::card>
                        </x-frontend::tabs.panel>
                    @endif
                @endforeach
            </div>
        </x-frontend::tabs.container>
    @endif

    <!-- Delete Confirmation Modal -->
    <div x-data="{ showDeleteModal: false }" @open-delete-modal.window="showDeleteModal = true">
        <x-frontend::modal show="showDeleteModal">
            <x-frontend::modal.header icon="phosphor-trash" iconColor="red" show="showDeleteModal">
                @lang('Delete Site')
            </x-frontend::modal.header>

            <x-frontend::modal.body>
                <div class="space-y-4">
                    <p class="text-base-100">
                        @lang('Are you sure you want to delete this site?')
                    </p>
                    <div class="bg-base-850 border border-base-700 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                @svg('phosphor-warning-circle', 'w-5 h-5 text-orange mt-0.5')
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-base-300">
                                    <span class="font-semibold text-base-100">{{ $site->url }}</span>
                                </p>
                                <p class="text-sm text-base-400 mt-1">
                                    @lang('This action cannot be undone. This will permanently delete the site and all associated monitors (uptime, lighthouse, crawler, etc.).')
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
                <form action="{{ route('site.delete', ['site' => $site]) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-form.button class="bg-red" type="submit">
                        @lang('Delete Site')
                    </x-form.button>
                </form>
            </x-frontend::modal.footer>
        </x-frontend::modal>
    </div>

</x-app-layout>
