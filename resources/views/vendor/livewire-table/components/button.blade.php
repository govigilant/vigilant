@props(['size' => 'md', 'active' => false, 'dot' => false])

<button
    {{
        $attributes->merge([
            'type' => 'button',
        ])->class([
            'relative flex items-center rounded-lg border cursor-pointer transition-all duration-200',
            'focus:outline-none focus:ring-2 focus:ring-red/50 focus:z-10',
            'bg-base-850 hover:bg-base-800 active:bg-base-800',
            'border-base-700 hover:border-base-600 focus:border-red',
            'text-base-100 hover:text-base-50 active:text-base-50' => ! $active,
            'text-red border-red bg-red/10' => $active,
            'px-3 py-2' => $size === 'md',
            'px-2 py-1' => $size === 'sm',
        ])
    }}
>
    {{ $slot }}
    @if($dot)
        <span class="absolute right-2 top-1 rounded-full shadow-xs bg-red block size-2"></span>
    @endif
</button>
