@props(['title', 'back'])
<div class="md:flex md:items-center md:justify-between">
    <div class="min-w-0 flex-1 flex items-center gap-4">
        @if(isset($back))
            <a href="{{ $back }}" wire:navigate.hover>
                @svg('tni-arrow-left-circle-o', 'w-6 h-6')
            </a>
        @endif
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100">{{ __($title) }}</h2>
    </div>
    <div class="mt-4 flex md:ml-4 md:mt-0">
        {{ $slot }}
    </div>
</div>

