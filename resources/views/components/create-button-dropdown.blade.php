@props(['model'])
@can('create', $model)
    <li {{ $attributes->merge(['class' => 'cursor-pointer text-white text-md p-3 transition-all hover:bg-red']) }}
        wire:navigate.hover>
        {{ $slot }}
    </li>
@else
    <li {{ $attributes->merge(['class' => 'cursor-pointer text-base-600 text-md p-3 transition-all cursor-not-allowed'])->except(['href']) }}
        x-on:click="open = false; alert('@lang('Your current plan does not allow to create this resource')')">
        <div class="flex items-center gap-2">
            @svg('tni-x-circle-o', 'size-5 text-red')
            <span>{{ $slot }}</span>
        </div>
    </li>
@endcan
