<div {{ $attributes->merge(['class' => 'mb-8 pb-6 border-b border-base-700/50 relative']) }}>
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
                    rgba(249, 115, 22, 0.005) 55%,
                    rgba(249, 115, 22, 0.01) 60%,
                    rgba(249, 115, 22, 0.015) 70%,
                    rgba(249, 115, 22, 0.02) 80%,
                    rgba(249, 115, 22, 0.025) 90%,
                    rgba(249, 115, 22, 0.03) 100%
                ),
                url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%221.2%22 numOctaves=%225%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.15%22/%3E%3C/svg%3E');"></div>
    
    <h3 class="text-xl sm:text-2xl font-bold leading-tight bg-gradient-to-r from-base-50 via-base-100 to-base-200 bg-clip-text text-transparent">
        {{ $slot }}
    </h3>
    <div class="h-1 w-16 rounded-full mt-2 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-red via-orange to-transparent"></div>
        <div class="absolute inset-0 opacity-30 mix-blend-soft-light" 
             style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%221%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E');"></div>
    </div>
</div>
