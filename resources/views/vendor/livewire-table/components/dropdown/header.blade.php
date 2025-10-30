@props(['label', 'icon', 'navigate' => null])

<header class="flex items-center border-b border-base-700 transition-all duration-200">
    <button
        {{
            $attributes->merge([
                'type' => 'button',
            ])->class([
                'flex items-center gap-3 w-full text-sm px-4 py-3 border transition-all duration-200',
                'bg-base-900',
                'border-base-900',
                'text-base-100',
                'cursor-pointer' => $navigate,
                'focus:outline-none focus:ring-2 focus:ring-red/50 focus:z-10' => $navigate,
                'hover:bg-base-800 active:bg-base-800' => $navigate,
                'hover:border-base-800 focus:border-red' => $navigate,
                'hover:text-base-50 active:text-base-50' => $navigate,
            ])->when($navigate, fn ($bag) => $bag->merge([
                'x-data' => Js::from(['navigate' => $navigate]),
                'x-on:click' => 'current = navigate',
            ]), fn ($bag) => $bag->merge([
                'disabled' => '',
            ]))
        }}
    >
        <x-livewire-table::icon class="text-base-400 size-5 transition-colors duration-200" :icon="$icon" />
        <span class="font-medium">{{ $label }}</span>
    </button>
    {{ $slot }}
</header>
