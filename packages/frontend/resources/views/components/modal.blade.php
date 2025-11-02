@props(['show' => 'show', 'maxWidth' => '2xl'])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-show="{{ $show }}"
    x-on:keydown.escape.window="{{ $show }} = false"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-[99] flex items-center justify-center"
    style="display: none;"
    x-cloak
>
    <div
        x-show="{{ $show }}"
        class="fixed inset-0 transform transition-all"
        x-on:click="{{ $show }} = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-base-black/80 backdrop-blur-sm"></div>
    </div>

    <div
        x-show="{{ $show }}"
        class="relative bg-base-900 rounded-lg overflow-hidden shadow-xl transform transition-all w-full mx-auto {{ $maxWidth }}"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        @click.away="{{ $show }} = false"
    >
        <div class="border border-base-700/50 rounded-lg overflow-hidden">
            {{ $slot }}
        </div>
    </div>
</div>

