<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('sites')" title="Site - {{ $site->url }}">
            <x-frontend::page-header.actions>
                <x-form.button dusk="site-edit-button" class="bg-blue hover:bg-blue-light" :href="route('site.edit', ['site' => $site])">
                    @lang('Edit')
                </x-form.button>
            </x-frontend::page-header.actions>

            <x-frontend::page-header.mobile-actions>
                <x-form.dropdown-button dusk="site-edit-button" :href="route('site.edit', ['site' => $site])">
                    @lang('Edit')
                </x-form.dropdown-button>
            </x-frontend::page-header.mobile-actions>

        </x-page-header>
    </x-slot>

    @if ($empty)
        <div class="mx-auto max-w-2xl text-center py-12">
            <x-card class="bg-base-850/50 border-base-700/50">
                <div class="flex flex-col items-center">
                    <div class="rounded-full bg-red/10 p-4 mb-6">
                        @svg('tni-folder-plus-o', 'h-12 w-12 text-red')
                    </div>

                    <h3 class="text-2xl font-bold text-base-50 mb-2">@lang('No Monitors Configured')</h3>

                    <p class="text-base text-base-300 mb-8 max-w-md">
                        @lang('Get started by adding monitors for this site')
                    </p>

                    <x-form.button class="bg-gradient-to-r from-red via-orange to-red bg-300% animate-gradient-shift hover:shadow-lg hover:shadow-red/30 transition-all duration-300" :href="route('site.edit', ['site' => $site])">
                        @lang('Configure Monitors')
                    </x-form.button>
                </div>
            </x-card>
        </div>
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
                                @endif
                            </x-frontend::card>
                        </x-frontend::tabs.panel>
                    @endif
                @endforeach
            </div>
        </x-frontend::tabs.container>
    @endif

</x-app-layout>
