@props(['title', 'back'])
@section('title', $title)

<div {{ $attributes->merge(['class' => 'relative mb-6']) }}>
    <!-- Subtle background glow -->
    <div class="absolute -inset-x-4 -inset-y-2 bg-gradient-to-r from-red/3 via-transparent to-blue/3 blur-xl -z-10">
    </div>

    <div class="flex items-center justify-between">
        <div class="min-w-0 flex items-center gap-4">
            @if (isset($back))
                <a href="{{ $back }}" wire:navigate.hover
                    class="flex items-center justify-center w-9 h-9 rounded-lg bg-base-850/50 border border-base-700 text-base-400 hover:text-base-50 hover:bg-base-800 hover:border-red/50 transition-all duration-200 group">
                    @svg('tni-arrow-left-circle-o', 'w-4 h-4 group-hover:-translate-x-0.5 transition-transform duration-200')
                </a>
            @endif
            <div>
                <h1
                    class="text-2xl sm:text-3xl font-bold leading-tight bg-gradient-to-r from-base-50 via-base-100 to-base-200 bg-clip-text text-transparent">
                    {{ __($title) }}
                </h1>
                <div class="h-1 w-16 bg-gradient-to-r from-red via-orange to-transparent rounded-full mt-1.5"></div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            {{ $slot }}
        </div>
    </div>
</div>
