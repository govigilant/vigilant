@props(['title', 'back'])
@section('title', $title)

<div x-data="{ show: true }" 
     @navigation-start.window="show = false" 
     @navigation-end.window="show = true"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     :style="show ? '' : 'opacity: 0'"
     {{ $attributes->merge(['class' => 'relative mb-6']) }}>
    <!-- Subtle background glow with noise to prevent banding -->
    <div class="absolute -inset-x-4 -inset-y-2 blur-xl -z-10" 
         style="background: 
                linear-gradient(90deg, 
                    rgba(239, 68, 68, 0.03) 0%, 
                    rgba(239, 68, 68, 0.025) 10%,
                    rgba(239, 68, 68, 0.02) 20%,
                    rgba(239, 68, 68, 0.015) 30%,
                    rgba(239, 68, 68, 0.01) 40%,
                    rgba(239, 68, 68, 0.005) 45%,
                    transparent 50%,
                    rgba(59, 130, 246, 0.005) 55%,
                    rgba(59, 130, 246, 0.01) 60%,
                    rgba(59, 130, 246, 0.015) 70%,
                    rgba(59, 130, 246, 0.02) 80%,
                    rgba(59, 130, 246, 0.025) 90%,
                    rgba(59, 130, 246, 0.03) 100%
                ),
                url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%221.2%22 numOctaves=%225%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.15%22/%3E%3C/svg%3E');">
    </div>

    <div class="flex items-center justify-between">
        <div class="min-w-0 flex items-center gap-4">
            @if (isset($back))
                <a href="{{ $back }}" wire:navigate.hover
                    class="relative flex items-center justify-center w-10 h-10 rounded-lg bg-base-850 border border-base-700 text-base-300 hover:text-base-50 hover:bg-base-800 hover:border-indigo transition-all duration-300 group overflow-hidden shadow-lg hover:shadow-indigo/20">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    @svg('tni-arrow-left-circle-o', 'w-5 h-5 relative z-10 group-hover:-translate-x-0.5 transition-transform duration-200')
                </a>
            @endif
            <div>
                <h1
                    class="text-2xl sm:text-3xl font-bold leading-tight bg-gradient-to-r from-base-50 via-base-100 to-base-200 bg-clip-text text-transparent">
                    {{ __($title) }}
                </h1>
                <div class="h-1 w-16 rounded-full mt-1.5 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-red via-orange to-transparent"></div>
                    <div class="absolute inset-0 opacity-30 mix-blend-soft-light" 
                         style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%221%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E');"></div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            {{ $slot }}
        </div>
    </div>
</div>
