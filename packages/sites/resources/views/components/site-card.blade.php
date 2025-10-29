@props(['site'])

@php
    use Vigilant\Lighthouse\Livewire\Tables\LighthouseMonitorsTable;
    use Vigilant\Uptime\Actions\CalculateUptimePercentage;
    use Vigilant\Frontend\Integrations\Table\Enums\Status;

    /** @var \Vigilant\Sites\Models\Site $site */
    
    // Calculate uptime
    $calculateUptime = app(CalculateUptimePercentage::class);
    $uptimeMonitor = $site->uptimeMonitor;
    $uptimePercentage = $uptimeMonitor ? $calculateUptime->calculate($uptimeMonitor) : null;
    
    // Get Lighthouse score
    $lighthouseMonitor = $site->lighthouseMonitors()->first();
    $lighthouseResult = $lighthouseMonitor?->lighthouseResults()->orderByDesc('id')->first();
    $lighthouseScore = null;
    if ($lighthouseResult) {
        $scores = [
            $lighthouseResult->performance,
            $lighthouseResult->accessibility,
            $lighthouseResult->best_practices,
            $lighthouseResult->seo,
        ];
        $lighthouseScore = array_sum($scores) / count($scores);
    }
    
    // Get last downtime
    $lastDowntime = null;
    if ($uptimeMonitor) {
        $lastDowntime = $uptimeMonitor->downtimes()
            ->whereNotNull('end')
            ->orderByDesc('start')
            ->first();
    }
    
    // Get link issues
    $crawler = $site->crawler;
    $issueCount = $crawler?->issueCount() ?? 0;
    $totalUrlCount = $crawler?->totalUrlCount() ?? 0;
    $issueStatus = null;
    if ($crawler) {
        if ($issueCount === 0) {
            $issueStatus = Status::Success;
        } else {
            $threshold = $totalUrlCount * 0.05;
            $issueStatus = $issueCount > $threshold ? Status::Danger : Status::Warning;
        }
    }
    
    // Get certificate info
    $certificate = $site->certificateMonitor;
    $certificateStatus = null;
    if ($certificate && $certificate->valid_to) {
        $diff = now()->diffInDays($certificate->valid_to);
        if ($diff > 30) {
            $certificateStatus = Status::Success;
        } elseif ($diff > 7) {
            $certificateStatus = Status::Warning;
        } else {
            $certificateStatus = Status::Danger;
        }
    }
@endphp

<a href="{{ route('site.view', ['site' => $site]) }}" 
   class="block group relative">
    <div class="border border-base-700 shadow-xl rounded-xl overflow-hidden backdrop-blur-sm relative transition-all duration-300 hover:shadow-2xl hover:shadow-indigo/10 hover:border-base-600">
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

        <!-- Hover glow effect -->
        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"
             style="background: radial-gradient(800px circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(99, 102, 241, 0.05), transparent 40%);"></div>
        
        <div class="relative p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <!-- Left side: Site URL and metrics -->
                <div class="flex-1 min-w-0">
                    <!-- Site URL Header -->
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold text-base-50 group-hover:text-indigo-light transition-colors duration-200 truncate">
                            {{ $site->url }}
                        </h3>
                        <div class="mt-2 h-0.5 w-16 bg-gradient-to-r from-indigo to-purple-light rounded-full group-hover:w-full transition-all duration-500"></div>
                    </div>

                    <!-- Metrics Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                        <!-- Lighthouse Score -->
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-base-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span class="text-xs uppercase tracking-wider text-base-400 font-medium">Lighthouse</span>
                            </div>
                            <div class="text-base-100">
                                @if ($lighthouseScore !== null)
                                    {!! LighthouseMonitorsTable::scoreDisplay($lighthouseScore) !!}
                                @elseif ($lighthouseResult === null && $lighthouseMonitor !== null)
                                    <span class="text-sm text-base-400">{{ __('No Results') }}</span>
                                @else
                                    <span class="text-base-500 text-2xl">&mdash;</span>
                                @endif
                            </div>
                        </div>

                        <!-- Uptime -->
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-base-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs uppercase tracking-wider text-base-400 font-medium">Uptime</span>
                            </div>
                            <div>
                                @if ($uptimePercentage !== null)
                                    @php
                                        $uptimeClass = match (true) {
                                            $uptimePercentage > 95 => 'text-green-light',
                                            $uptimePercentage > 80 => 'text-orange',
                                            default => 'text-red'
                                        };
                                    @endphp
                                    <span class="{{ $uptimeClass }} text-2xl font-bold">{{ $uptimePercentage }}%</span>
                                @elseif ($uptimeMonitor !== null)
                                    <span class="text-sm text-base-400">{{ __('Not available yet') }}</span>
                                @else
                                    <span class="text-base-500 text-2xl">&mdash;</span>
                                @endif
                            </div>
                        </div>

                        <!-- Last Downtime -->
                        @if ($uptimeMonitor !== null)
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-base-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-xs uppercase tracking-wider text-base-400 font-medium">Last downtime</span>
                                </div>
                                <div class="text-base-200 text-sm font-medium">
                                    @if ($lastDowntime !== null)
                                        {{ teamTimezone($lastDowntime->start)->diffForHumans() }}
                                    @else
                                        {{ __('Never') }}
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Link Issues -->
                        @if ($crawler !== null)
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-base-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                    <span class="text-xs uppercase tracking-wider text-base-400 font-medium">Link Issues</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-none rounded-full p-1 bg-base-800/50">
                                        @if ($issueStatus === Status::Success)
                                            <div class="h-2 w-2 rounded-full bg-green-light"></div>
                                        @elseif($issueStatus === Status::Warning)
                                            <div class="h-2 w-2 rounded-full bg-orange-light animate-pulse"></div>
                                        @else
                                            <div class="h-2 w-2 rounded-full bg-red-light animate-pulse"></div>
                                        @endif
                                    </div>
                                    <span class="text-sm font-medium text-base-200">
                                        {{ __(':count issues', ['count' => $issueCount]) }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right side: Status indicators and action -->
                <div class="flex flex-col items-end gap-4 lg:min-w-[280px]">
                    <!-- Certificate -->
                    @if ($certificate !== null && $certificate->valid_to !== null)
                        <div class="flex items-center gap-3 bg-base-800/30 rounded-lg px-4 py-3 border border-base-700/50 w-full">
                            <div class="flex-none rounded-full p-1 bg-base-800/50">
                                @if ($certificateStatus === Status::Success)
                                    <div class="h-2 w-2 rounded-full bg-green-light"></div>
                                @elseif($certificateStatus === Status::Warning)
                                    <div class="h-2 w-2 rounded-full bg-orange-light animate-pulse"></div>
                                @else
                                    <div class="h-2 w-2 rounded-full bg-red-light animate-pulse"></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs text-base-400 uppercase tracking-wider">Certificate</div>
                                <div class="text-sm font-medium text-base-200 truncate">
                                    {{ __('Expires in :diff', ['diff' => teamTimezone($certificate->valid_to)->longAbsoluteDiffForHumans()]) }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- View details button -->
                    <div class="flex items-center gap-2 text-indigo group-hover:text-indigo-light transition-colors duration-200">
                        <span class="text-sm font-medium">{{ __('View details') }}</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</a>
