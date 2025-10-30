<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-base-black relative">
    <!-- Noise overlay -->
    <div class="absolute inset-0 opacity-[0.04]" style="background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMDAiIGhlaWdodD0iMzAwIj48ZmlsdGVyIGlkPSJhIiB4PSIwIiB5PSIwIj48ZmVUdXJidWxlbmNlIGJhc2VGcmVxdWVuY3k9Ii43NSIgc3RpdGNoVGlsZXM9InN0aXRjaCIgdHlwZT0iZnJhY3RhbE5vaXNlIi8+PGZlQ29sb3JNYXRyaXggdHlwZT0ic2F0dXJhdGUiIHZhbHVlcz0iMCIvPjwvZmlsdGVyPjxwYXRoIGQ9Ik0wIDBoMzAwdjMwMEgweiIgZmlsdGVyPSJ1cmwoI2EpIiBvcGFjaXR5PSIuMDUiLz48L3N2Zz4=');"></div>
    
    <!-- Decorative gradient orbs -->
    <div class="absolute top-20 left-10 w-96 h-96 bg-gradient-to-br from-red/20 to-orange/10 rounded-full blur-3xl opacity-30 animate-pulse-glow"></div>
    <div class="absolute bottom-20 right-10 w-80 h-80 bg-gradient-to-br from-blue/20 to-indigo/10 rounded-full blur-3xl opacity-20 animate-pulse-glow" style="animation-delay: -2s;"></div>
    
    <div class="relative z-10">
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-base-900 border border-base-700 shadow-xl overflow-hidden sm:rounded-lg relative z-10">
        {{ $slot }}
    </div>
</div>
