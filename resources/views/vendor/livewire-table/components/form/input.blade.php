@props(['size' => 'md'])

<input
    {{
        $attributes->class([
            'w-full rounded-lg border transition-all duration-200',
            'focus:outline-none focus:ring-2 focus:ring-red/50 focus:z-10',
            'bg-base-800 hover:bg-base-700 active:bg-base-700',
            'border-base-700 hover:border-base-600 focus:border-red',
            'text-base-100 placeholder:text-base-400',
            'px-3 py-2' => $size === 'md',
            'px-2 py-1' => $size === 'sm',
        ])
    }}
/>
