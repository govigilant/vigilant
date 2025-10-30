@props(['padding' => true])

<div {{ $attributes->merge(['class' => 'border border-base-700 shadow-xl rounded-xl overflow-hidden backdrop-blur-sm relative ' . ($padding ? 'px-6 py-8 sm:p-8' : '')]) }}>
    <!-- Multi-stop gradient background to prevent banding -->
    <div class="absolute inset-0 -z-10" 
         style="background: 
                linear-gradient(135deg, 
                    rgba(35, 35, 51, 1) 0%, 
                    rgba(33, 33, 48, 1) 10%,
                    rgba(31, 31, 45, 1) 20%,
                    rgba(29, 29, 42, 1) 30%,
                    rgba(28, 28, 40, 1) 40%,
                    rgba(27, 27, 38, 1) 50%,
                    rgba(26, 26, 36, 1) 60%,
                    rgba(26, 26, 36, 1) 70%,
                    rgba(26, 26, 36, 1) 80%,
                    rgba(26, 26, 36, 1) 90%,
                    rgba(26, 26, 36, 1) 100%
                ),
                url('data:image/svg+xml,%3Csvg viewBox=%220 0 400 400%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%221.5%22 numOctaves=%225%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.12%22/%3E%3C/svg%3E');">
    </div>
    
    <!-- Subtle gradient overlay for depth with noise -->
    <div class="absolute inset-0 pointer-events-none"
         style="background: 
                linear-gradient(180deg, 
                    rgba(45, 45, 66, 0.1) 0%, 
                    rgba(45, 45, 66, 0.075) 10%,
                    rgba(45, 45, 66, 0.05) 20%,
                    rgba(45, 45, 66, 0.025) 30%,
                    rgba(45, 45, 66, 0.01) 40%,
                    transparent 50%
                ),
                url('data:image/svg+xml,%3Csvg viewBox=%220 0 300 300%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22grainFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%221%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23grainFilter)%22 opacity=%220.08%22/%3E%3C/svg%3E');"></div>
    
    <div class="relative">
        {{ $slot }}
    </div>
</div>

