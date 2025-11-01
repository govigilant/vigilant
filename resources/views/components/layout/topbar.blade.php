<div class="sticky top-0 z-20 flex h-12 sm:h-14 shrink-0 items-center gap-x-3 bg-base-black px-4 sm:px-6 lg:px-8">
    <!-- Mobile menu button -->
    <button type="button" class="group -m-2 p-2 text-base-400 hover:text-base-200 transition-colors duration-200 lg:hidden" x-on:click="sidebarOpen = true">
        <span class="sr-only">Open sidebar</span>
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <div class="flex flex-1 gap-x-3 self-stretch">
        <div class="flex-1"></div>
        <div class="flex items-center gap-x-2">
            <!-- Team Switcher -->
            @if (Laravel\Jetstream\Jetstream::hasTeamFeatures() && Auth::user() !== null)
                <div class="relative" x-data="{ open: false }">
                    <button type="button" 
                        class="group flex items-center gap-x-2 rounded-lg px-2.5 py-1.5 text-sm font-semibold bg-base-900/50 border border-base-800/50 text-base-100 hover:bg-base-800/50 hover:border-base-700 transition-all duration-200"
                        x-on:click="open = !open">
                        <!-- Team icon -->
                        <svg class="h-4 w-4 text-base-400 group-hover:text-base-200 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                        <span class="truncate max-w-[150px]">{{ Auth::user()?->currentTeam?->name ?? '-' }}</span>
                        <svg class="h-3.5 w-3.5 text-base-400 group-hover:text-base-200 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                        </svg>
                    </button>

                    <div x-show="open" 
                         x-on:click.outside="open = false" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 z-10 mt-2 w-60 origin-top-right rounded-lg bg-base-950 border border-base-800/50 shadow-lg overflow-hidden"
                         role="menu">
                        <!-- Header -->
                        <div class="px-4 py-3 border-b border-base-800/50 bg-base-900/30">
                            <p class="text-xs font-semibold text-base-400 uppercase tracking-wider">{{ __('Manage Team') }}</p>
                        </div>

                        <!-- Team Settings -->
                        <div class="py-1">
                            <a href="{{ route('settings', ['tab' => 'team']) }}" 
                               class="group flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-base-300 hover:text-base-50 hover:bg-base-900/50 transition-all duration-200">
                                <div class="flex items-center justify-center w-7 h-7 rounded-lg transition-all duration-200 group-hover:bg-base-800">
                                    <svg class="h-4 w-4 text-base-500 group-hover:text-base-200 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                {{ __('Team Settings') }}
                            </a>

                            @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                <a href="{{ route('teams.create') }}" 
                                   class="group flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-base-300 hover:text-base-50 hover:bg-base-900/50 transition-all duration-200">
                                    <div class="flex items-center justify-center w-7 h-7 rounded-lg transition-all duration-200 group-hover:bg-base-800">
                                        <svg class="h-4 w-4 text-base-500 group-hover:text-base-200 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </div>
                                    {{ __('Create New Team') }}
                                </a>
                            @endcan
                        </div>

                        @if (Auth::user()->allTeams()->count() > 1)
                            <!-- Switch Teams Section -->
                            <div class="border-t border-base-800/50">
                                <div class="px-4 py-3 bg-base-900/30">
                                    <p class="text-xs font-semibold text-base-400 uppercase tracking-wider">{{ __('Switch Teams') }}</p>
                                </div>
                                <div class="py-1">
                                    @foreach (Auth::user()->allTeams() as $team)
                                        <x-switchable-team :team="$team" />
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Notifications -->
            <a href="{{ route('notifications.history') }}" 
               class="notification-bell group relative p-2 rounded-lg text-base-400 hover:text-red hover:bg-base-900/50 transition-all duration-200">
                <span class="sr-only">@lang('View notifications')</span>
                <svg class="h-5 w-5 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
            </a>
        </div>
    </div>
</div>
