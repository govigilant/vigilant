@props(['label', 'icon' => null, 'navigate' => null, 'dot' => false])

<li>
    <button
        {{
            $attributes->merge([
                'type' => 'button',
            ])->class([
                'flex items-center gap-3 w-full px-4 py-2 text-left relative border group/item cursor-pointer transition-all duration-200 disabled:cursor-not-allowed',
                'focus:outline-none focus:ring-2 focus:ring-red/50 focus:z-10',
                'hover:bg-base-800 active:bg-base-800 disabled:hover:bg-base-850 disabled:active:bg-base-850',
                'border-base-850 hover:border-base-800 focus:border-red disabled:hover:border-base-850',
                'text-base-100 hover:text-base-50 active:text-base-50 disabled:text-base-500 disabled:hover:text-base-500',
            ])->when($navigate, fn ($bag) => $bag->merge([
                'x-data' => Js::from(['navigate' => $navigate]),
                'x-on:click' => 'current = navigate',
            ]))
        }}
    >
        @if($icon)
            <x-livewire-table::icon class="text-base-400 group-hover/item:text-base-300 group-active/item:text-base-300 transition-colors duration-200 size-5" :icon="$icon" />
        @endif

        <span class="relative flex-1">
            {{ $label }}
        </span>

        @if($navigate)
            <x-livewire-table::icon class="text-base-400 group-hover/item:text-base-300 group-active/item:text-base-300 transition-colors duration-200 size-5" icon="chevron-right" />
        @endif

        @if($dot)
            <span class="absolute left-8 top-2 rounded-full shadow-xs bg-red block size-2"></span>
        @endif
    </button>
</li>
