@props([
    'tabs' => [],
])

<div {{ $attributes->merge(['class' => 'mb-8']) }}>
    <x-frontend::card :padding="false" class="overflow-hidden">
        <div class="border-b border-base-700">
            <nav class="-mb-px flex space-x-1 p-2" aria-label="Tabs">
                @foreach($tabs as $index => $tab)
                    @if(!isset($tab['gate']) || auth()->user()->can($tab['gate']))
                        <button
                            @click="activeTab = '{{ $tab['key'] }}'"
                            :class="activeTab === '{{ $tab['key'] }}' ? 'border-{{ $tab['color'] ?? 'red' }} text-base-50 bg-base-800/50' : 'border-transparent text-base-300 hover:text-base-100 hover:border-base-600'"
                            class="group relative min-w-0 flex-1 sm:flex-initial overflow-hidden rounded-lg border-2 px-4 py-3 text-center text-sm font-medium transition-all duration-300 focus:z-10 focus:outline-none focus:ring-2 focus:ring-{{ $tab['color'] ?? 'red' }} focus:ring-offset-2 focus:ring-offset-base-900 @if($index === 0) border-{{ $tab['color'] ?? 'red' }} text-base-50 bg-base-800/50 @else border-transparent text-base-300 @endif"
                        >
                            <span class="flex items-center justify-center gap-2">
                                @if(isset($tab['icon']))
                                    @svg($tab['icon'], 'size-4')
                                @endif
                                <span class="hidden sm:inline">{{ $tab['label'] }}</span>
                            </span>
                        </button>
                    @endif
                @endforeach
            </nav>
        </div>
    </x-frontend::card>
</div>
