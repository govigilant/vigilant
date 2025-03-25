@props(['title', 'back'])
<div {{ $attributes->merge(['class' => 'flex items-center justify-between']) }}>
    <div class="min-w-0 flex items-center gap-4">
        @if (isset($back))
            <a href="{{ $back }}" wire:navigate.hover>
                @svg('tni-arrow-left-circle-o', 'w-6 h-6')
            </a>
        @endif
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100">
            {{ __($title) }}</h2>
    </div>
    <div class="md:ml-4 gap-4">
        {{ $slot }}
    </div>
</div>
