@props(['size' => 'md'])

<label class="block relative">
    <select
        {{
            $attributes->class([
                'w-full rounded-lg border appearance-none transition-all duration-200',
                'focus:outline-none focus:ring-2 focus:ring-red/50 focus:z-10',
                'bg-base-800 hover:bg-base-700 active:bg-base-700',
                'border-base-700 hover:border-base-600 focus:border-red',
                'text-base-100',
                'pl-3 pr-10 py-2' => $size === 'md',
                'pl-2 pr-6 py-1' => $size === 'sm',
            ])
        }}
    >
        {{ $slot }}
    </select>
    @if($size === 'sm')
        <x-livewire-table::icon icon="chevron-down" class="pointer-events-none size-3 absolute right-2 top-3 text-base-100 transition-colors duration-200" />
    @elseif($size === 'md')
        <x-livewire-table::icon icon="chevron-down" class="pointer-events-none size-4.5 absolute right-3 top-3 text-base-100 transition-colors duration-200" />
    @endif
</label>
