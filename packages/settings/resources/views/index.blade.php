<x-slot name="header">
    <x-page-header title="Settings">
    </x-page-header>
</x-slot>
<div>

    <div x-data="{ selectedTab: @entangle('tab') }">
        <div class="max-w-7xl mx-auto mb-8">
            <!-- Mobile dropdown -->
            <div class="sm:hidden">
                <label for="tabs" class="sr-only">{{ __('Select a tab') }}</label>
                <select name="tabs"
                        id="tabs"
                        x-model="selectedTab"
                        class="block w-full rounded-lg border border-base-700 bg-base-850 py-2.5 pl-3 pr-10 text-base-100 shadow-sm focus:border-red focus:ring-2 focus:ring-red/20 transition-colors">
                    @foreach($tabs as $key => $data)
                        <option value="{{ $key }}">{{ $data['title']  }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Desktop tabs -->
            <div class="hidden sm:block">
                <nav class="flex gap-2 p-1 bg-base-850/50 rounded-lg border border-base-700/50 backdrop-blur-sm">
                    @foreach($tabs as $key => $data)
                        <button
                            type="button"
                            x-on:click="selectedTab = '{{ $key }}'"
                            :class="selectedTab == '{{ $key }}' 
                                ? 'bg-gradient-to-r from-red to-orange text-white shadow-lg shadow-red/20' 
                                : 'text-base-300 hover:text-base-100 hover:bg-base-800/50'"
                            class="flex-1 px-4 py-2.5 text-sm font-medium rounded-md transition-all duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red">
                            {{ $data['title'] }}
                        </button>
                    @endforeach
                </nav>
            </div>
        </div>

        <div class="pb-10">
            @foreach($tabs as $key => $data)
                <div x-show="selectedTab == '{{ $key }}'" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-cloak>
                    @if(array_key_exists('component', $data))
                        <livewire:dynamic-component :is="$data['component']"
                                                    wire:key="{{ $key }}"/>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

</div>
