@props(['title', 'icon' => null, 'trend' => null, 'trendUp' => null])

<div class="group relative overflow-hidden rounded-xl border border-base-700 bg-base-850/50 backdrop-blur-sm p-6 transition-all duration-300 hover:border-base-600 hover:-translate-y-1 hover:shadow-xl hover:shadow-base-900/50"
     x-data="{
         contentLength: 0,
         fontSize: 'text-3xl md:text-4xl',
         init() {
             this.$nextTick(() => {
                 const content = this.$refs.content.innerText.trim();
                 this.contentLength = content.length;
                 
                 // Adjust font size based on content length
                 if (this.contentLength > 50) {
                     this.fontSize = 'text-sm md:text-base';
                 } else if (this.contentLength > 30) {
                     this.fontSize = 'text-base md:text-lg';
                 } else if (this.contentLength > 20) {
                     this.fontSize = 'text-lg md:text-xl';
                 } else if (this.contentLength > 10) {
                     this.fontSize = 'text-xl md:text-2xl';
                 } else {
                     this.fontSize = 'text-3xl md:text-4xl';
                 }
             });
         }
     }">
    <!-- Gradient background overlay with noise to prevent banding -->
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
                url('data:image/svg+xml,%3Csvg viewBox=%220 0 400 400%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%221.5%22 numOctaves=%225%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.15%22/%3E%3C/svg%3E');">
    </div>
    
    <!-- Gradient accent line on top -->
    <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-blue via-indigo to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    
    <!-- Glow effect on hover -->
    <div class="absolute -inset-0.5 bg-gradient-to-r from-blue/20 to-indigo/20 opacity-0 group-hover:opacity-100 blur-xl transition-opacity duration-500 -z-10"></div>
    
    <div class="relative">
        <!-- Header with icon and trend -->
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
                @if($icon)
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-blue/10 to-indigo/10 border border-blue/20 group-hover:border-blue/40 transition-colors duration-300">
                        @svg($icon, 'w-5 h-5 text-blue-light')
                    </div>
                @endif
                <dt class="text-sm font-medium text-base-300 group-hover:text-base-200 transition-colors duration-300">
                    {{ $title }}
                </dt>
            </div>
            
            @if($trend !== null)
                <div @class([
                    'flex items-center gap-1 px-2 py-1 rounded-md text-xs font-semibold transition-all duration-300',
                    'bg-green/10 text-green-light border border-green/30' => $trendUp,
                    'bg-red/10 text-red-light border border-red/30' => !$trendUp,
                ])>
                    @if($trendUp)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    @else
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    @endif
                    <span>{{ $trend }}%</span>
                </div>
            @endif
        </div>
        
        <!-- Value with dynamic font size -->
        <dd x-ref="content" 
            :class="fontSize"
            class="font-bold tracking-tight bg-gradient-to-r from-base-50 to-base-100 bg-clip-text text-transparent group-hover:from-base-50 group-hover:via-base-50 group-hover:to-base-100 transition-all duration-300 break-words">
            {{ $slot }}
        </dd>
        
        <!-- Decorative gradient line -->
        <div class="mt-4 h-1 w-12 rounded-full bg-gradient-to-r from-blue via-indigo to-transparent opacity-60 group-hover:opacity-100 group-hover:w-20 transition-all duration-500"></div>
    </div>
</div>
