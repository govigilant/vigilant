<div class="relative z-50 lg:hidden" role="dialog" aria-modal="true" x-cloak x-show="sidebarOpen">
    <!-- Backdrop with proper opacity -->
    <div class="fixed inset-0 bg-base-black/80 backdrop-blur-sm" x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-300" 
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" 
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0"></div>

    <div class="fixed inset-0 flex">
        <div class="relative mr-16 flex w-full max-w-xs flex-1" x-show="sidebarOpen"
            x-on:click.outside="sidebarOpen = false" 
            x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full" 
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform" 
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full">

            <!-- Close button -->
            <div class="absolute left-full top-0 flex w-16 justify-center pt-5"
                x-transition:enter="ease-in-out duration-300" 
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in-out duration-300"
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0">
                <button type="button" class="group -m-2 p-2 rounded-lg hover:bg-base-900/50 transition-all duration-200" x-on:click="sidebarOpen = false">
                    <span class="sr-only">Close sidebar</span>
                    <svg class="h-6 w-6 text-base-300 group-hover:text-base-100 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Sidebar panel -->
            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-base-950 px-6 pb-4 border-r border-base-800/50">
                <!-- Logo -->
                <div class="flex h-16 shrink-0 items-center pt-4">
                    <a href="/" wire:navigate.hover class="group">
                        <img class="h-16 w-auto transition-transform duration-300 group-hover:scale-105" src="{{ url('img/logo.svg') }}" alt="{{ config('app.name') }}">
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                @foreach (\Vigilant\Core\Facades\Navigation::items() as $item)
                                    @continue(!$item->shouldRender())
                                    <li>
                                        <a href="{{ $item->url }}" wire:navigate.hover @class([
                                            'group flex gap-x-3 rounded-lg px-3 py-2.5 text-sm font-semibold transition-all duration-200',
                                            'bg-base-900/50 text-base-100 border border-base-800/50' => $item->active(),
                                            'text-base-400 hover:text-base-100 hover:bg-base-900/30' => !$item->active(),
                                        ])>
                                            @if ($item->icon !== null)
                                                @svg($item->icon, 'h-5 w-5 shrink-0 transition-colors duration-200' . ($item->active() ? ' text-red' : ' text-base-500 group-hover:text-base-300'))
                                            @endif
                                            {{ __($item->name) }}
                                        </a>

                                        @if ($item->hasChildren())
                                            <ul class="mt-2 ml-3 pl-4 border-l-2 border-red/30 space-y-1">
                                                @foreach ($item->getChildren() as $child)
                                                    <li>
                                                        <a href="{{ $child->url }}" wire:navigate.hover
                                                            @class([
                                                                'group flex gap-x-3 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200',
                                                                'text-base-100' => $child->active(),
                                                                'text-base-400 hover:text-base-100 hover:bg-base-900/30' => !$child->active(),
                                                            ])>
                                                            <span class="flex items-center gap-x-2">
                                                                @if ($child->icon !== null)
                                                                    @svg($child->icon, 'h-4 w-4 shrink-0 transition-colors duration-200' . ($child->active() ? ' text-red' : ' text-base-500 group-hover:text-base-300'))
                                                                @endif
                                                                {{ __($child->name) }}
                                                            </span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </li>

                        <!-- User Profile at bottom -->
                        <li class="mt-auto">
                            <!-- Gradient divider -->
                            <div class="mb-4 h-px bg-gradient-to-r from-transparent via-base-700 to-transparent"></div>
                            
                            <x-layout.user-profile-dropdown />
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
