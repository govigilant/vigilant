@props([
    'tabs' => [],
])

<div {{ $attributes->merge(['class' => 'space-y-6']) }}>
    @foreach($tabs as $index => $tab)
        @if(!isset($tab['gate']) || auth()->user()->can($tab['gate']))
            <div x-show="activeTab === '{{ $tab['key'] }}'" 
                 @if($index !== 0)x-cloak @endif
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="space-y-6">
                
                @if(isset($tab['title']) || isset($tab['description']) || isset($tab['route']))
                    <div class="flex items-center justify-between">
                        <div>
                            @if(isset($tab['title']))
                                <h2 class="text-2xl font-bold text-base-50 flex items-center gap-3">
                                    @if(isset($tab['icon']))
                                        @svg($tab['icon'], 'size-6 text-' . ($tab['color'] ?? 'red'))
                                    @endif
                                    {{ $tab['title'] }}
                                </h2>
                            @endif
                            @if(isset($tab['description']))
                                <p class="text-base-300 mt-1">{{ $tab['description'] }}</p>
                            @endif
                        </div>
                        @if(isset($tab['route']))
                            <a href="{{ $tab['route'] }}" 
                               class="group flex items-center gap-2 px-4 py-2.5 rounded-lg border-2 border-base-700 bg-base-850/50 hover:border-{{ $tab['color'] ?? 'red' }} hover:bg-base-800/50 text-base-200 hover:text-base-50 transition-all duration-300 text-sm font-medium">
                                <span>@lang('View Details')</span>
                                @svg('tni-right-o', 'size-4 group-hover:translate-x-1 transition-transform duration-300')
                            </a>
                        @endif
                    </div>
                @endif

                <x-frontend::card>
                    {{ $slot }}
                </x-frontend::card>
            </div>
        @endif
    @endforeach
</div>
