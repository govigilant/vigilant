@props(['model'])
@can('create', $model)
    <li {{ $attributes->merge(['class' => 'cursor-pointer text-base-100 text-sm font-medium p-3 transition-all hover:bg-gradient-to-r hover:from-red/20 hover:to-orange/20 rounded-lg']) }}
        wire:navigate.hover>
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>{{ $slot }}</span>
        </div>
    </li>
@else
    <li {{ $attributes->merge(['class' => 'cursor-not-allowed text-base-400 text-sm p-3 transition-all opacity-60'])->except(['href']) }}
        x-on:click="open = false; alert('@lang('Your current plan does not allow to create this resource')')">
        <div class="flex items-center gap-2">
            @svg('tni-x-circle-o', 'size-5 text-red')
            <span>{{ $slot }}</span>
        </div>
    </li>
@endcan
