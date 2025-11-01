@props(['model'])
@can('create', $model)
    <x-form.button {{ $attributes->merge(['class' => 'bg-gradient-to-r from-red via-orange to-red bg-[length:200%] bg-left hover:bg-right transition-[background-position] duration-300 border-transparent']) }} wire:navigate.hover>
        <span class="flex items-center gap-2">
            {{ $slot }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </span>
    </x-form.button>
@else
    <x-form.button
        {{ $attributes->merge(['class' => 'bg-base-700/50 hover:bg-base-600/50 cursor-not-allowed has-tooltip opacity-60 border-base-600/50'])->except(['href']) }}
        disabled>
        <span class="tooltip rounded-lg shadow-lg bg-base-900 border border-base-700 text-base-100 mt-8 px-3 py-2 text-xs">
            @lang('Your current plan does not allow to create this resource')
        </span>
        <span>
            {{ $slot }}
        </span>
    </x-form.button>
@endcan
