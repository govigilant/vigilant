<x-slot name="header">
    <x-page-header title="Settings">
    </x-page-header>
</x-slot>

<div x-data="{ activeTab: @entangle('tab') }" class="pb-10">
    
    {{-- Tab Navigation --}}
    <div class="max-w-7xl mx-auto mb-8">
        <x-frontend::card :padding="false" class="overflow-hidden">
            <div class="border-b border-base-700">
                <nav class="-mb-px flex space-x-1 p-2" aria-label="Tabs">
                    @foreach($tabs as $key => $data)
                        <button
                            type="button"
                            @click="activeTab = '{{ $key }}'"
                            :class="activeTab === '{{ $key }}' ? 'border-red text-base-50 bg-base-800/50' : 'border-transparent text-base-300 hover:text-base-100 hover:border-base-600'"
                            class="group relative min-w-0 flex-1 sm:flex-initial overflow-hidden rounded-lg border-2 px-4 py-3 text-center text-sm font-medium transition-all duration-300 focus:z-10 focus:outline-none focus:ring-2 focus:ring-red focus:ring-offset-2 focus:ring-offset-base-900 @if($loop->first) border-red text-base-50 bg-base-800/50 @else border-transparent text-base-300 @endif"
                        >
                            <span class="flex items-center justify-center gap-2">
                                <span>{{ $data['title'] }}</span>
                            </span>
                        </button>
                    @endforeach
                </nav>
            </div>
        </x-frontend::card>
    </div>

    {{-- Tab Panels --}}
    <div class="space-y-6">
        @foreach($tabs as $key => $data)
            <div x-show="activeTab === '{{ $key }}'" 
                 @if(!$loop->first)x-cloak @endif
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0">
                @if(array_key_exists('component', $data))
                    <livewire:dynamic-component :is="$data['component']" wire:key="{{ $key }}" />
                @endif
            </div>
        @endforeach
    </div>

</div>

