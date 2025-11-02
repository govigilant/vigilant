@props(['icon' => null, 'iconColor' => 'red', 'show' => 'show'])

<div class="px-6 py-5 bg-gradient-to-r from-base-950 to-base-900 border-b border-base-800/50">
    <div class="flex items-center gap-4">
        @if($icon)
            <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 rounded-lg bg-{{ $iconColor }}/10 border border-{{ $iconColor }}/30">
                @svg($icon, 'w-6 h-6 text-' . $iconColor)
            </div>
        @endif
        <div class="flex-1">
            <h3 class="text-xl font-bold text-base-50">
                {{ $slot }}
            </h3>
        </div>
        <button 
            type="button" 
            @click="{{ $show }} = false"
            class="flex-shrink-0 text-base-400 hover:text-base-200 transition-colors duration-200 p-1 rounded-lg hover:bg-base-800/50"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
