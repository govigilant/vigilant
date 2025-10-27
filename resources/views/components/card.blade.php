@props(['padding' => true])

<div {{ $attributes->merge(['class' => 'bg-gradient-to-br from-base-850 to-base-900 border border-base-700 shadow-xl rounded-xl overflow-hidden backdrop-blur-sm ' . ($padding ? 'px-6 py-8 sm:p-8' : '')]) }}>
    <!-- Subtle gradient overlay for depth -->
    <div class="absolute inset-0 bg-gradient-to-b from-base-800/10 to-transparent pointer-events-none"></div>
    <div class="relative">
        {{ $slot }}
    </div>
</div>
