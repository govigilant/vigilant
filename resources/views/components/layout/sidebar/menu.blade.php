<div class="hidden h-full lg:inset-y-0 lg:z-40 lg:flex lg:w-72 lg:flex-col relative overflow-hidden">
    <!-- Floating orbs -->
    <div class="absolute right-4 top-24 w-2 h-2 rounded-full bg-blue blur-sm pointer-events-none opacity-50 animate-float"></div>
    <div class="absolute right-6 top-64 w-1.5 h-1.5 rounded-full bg-indigo blur-sm pointer-events-none opacity-40 animate-float" style="animation-delay: -2s;"></div>
    <div class="absolute right-3 bottom-32 w-2.5 h-2.5 rounded-full bg-blue-light blur-sm pointer-events-none opacity-50 animate-float" style="animation-delay: -4s;"></div>
    
    <div class="flex grow flex-col gap-y-6 overflow-y-auto overflow-x-hidden dark:bg-base-black px-6 pb-4 relative">
        <!-- Radial glow emanating from edge -->
        <div class="absolute -right-32 top-2/3 w-64 h-64 bg-blue/10 rounded-full blur-3xl pointer-events-none animate-pulse-glow" style="animation-delay: -2s;"></div>
        
        <!-- Logo with subtle glow -->
        <div class="flex h-16 shrink-0 items-center pt-4">
            <a href="/" class="inline-block transition-transform duration-300 hover:scale-105">
                <img class="h-14 w-auto"
                    src="{{ url('img/logo.svg') }}" alt="{{ config('app.name') }}">
            </a>
        </div>

        <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                <li>
                    <ul role="list" class="-mx-2 space-y-1.5">
                        @foreach (\Vigilant\Core\Facades\Navigation::items() as $item)
                            @continue(!$item->shouldRender())
                            @php
                                $activeChild = false;
                                if ($item->hasChildren()) {
                                    foreach ($item->getChildren() as $child) {
                                        if ($child->active()) {
                                            $activeChild = true;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <li x-data="{ showChildren: {{ $item->active() || $activeChild ? 'true' : 'false' }} }">
                                <a @if ($item->url !== null) href="{{ $item->url }}" wire:navigate.hover
                                @else
                                    href="#" x-on:click.prevent="showChildren = !showChildren" @endif
                                    @class([
                                        'group relative flex items-center gap-x-3 rounded-lg p-2.5 text-sm leading-6 font-semibold transition-all duration-200',
                                        'bg-gradient-to-r from-red/10 to-transparent text-base-50 shadow-sm shadow-red/5' =>
                                            $item->active() || $activeChild,
                                        'text-base-300 hover:text-base-50 hover:bg-base-900/50' => !$item->active(),
                                    ])>
                                    @if ($item->active() || $activeChild)
                                        <div
                                            class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-gradient-to-b from-red to-orange rounded-r-full">
                                        </div>
                                    @endif

                                    @if ($item->icon !== null)
                                        <div
                                            class="flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 {{ $item->active() || $activeChild ? 'bg-red/20' : 'group-hover:bg-base-800' }}">
                                            @svg($item->icon, 'h-5 w-5 shrink-0 transition-all duration-200 ' . ($item->active() || $activeChild ? 'text-red' : 'text-base-400 group-hover:text-base-200'))
                                        </div>
                                    @endif
                                    <span class="flex-1">{{ __($item->name) }}</span>

                                    @if ($item->hasChildren() || $activeChild)
                                        <div class="transition-transform duration-200"
                                            x-bind:class="showChildren ? 'rotate-180' : ''">
                                            @svg('heroicon-o-chevron-down', 'h-4 w-4')
                                        </div>
                                    @endif
                                </a>

                                @if ($item->hasChildren())
                                    <ul class="mt-1.5 ml-11 space-y-1 overflow-hidden" x-show="showChildren" x-cloak
                                        x-collapse>
                                        @foreach ($item->getChildren() as $child)
                                            <li
                                                class="relative pl-4 border-l-2 border-base-800 hover:border-red/30 transition-colors duration-200">
                                                <a href="{{ $child->url }}" wire:navigate.hover
                                                    @class([
                                                        'group flex items-center gap-x-2 rounded-lg p-2 text-sm leading-6 font-medium transition-all duration-200',
                                                        'text-base-50 bg-base-900/30' => $child->active(),
                                                        'text-base-400 hover:text-base-50 hover:bg-base-900/30' => !$child->active(),
                                                    ])>
                                                    @if ($child->active())
                                                        <div
                                                            class="absolute -left-[2px] top-1/2 -translate-y-1/2 w-0.5 h-6 bg-red rounded-r-full">
                                                        </div>
                                                    @endif

                                                    @if ($child->icon !== null)
                                                        @svg($child->icon, 'h-4 w-4 shrink-0 transition-colors duration-200 ' . ($child->active() ? 'text-red' : 'text-base-500 group-hover:text-base-300'))
                                                    @else
                                                        <div
                                                            class="w-1.5 h-1.5 rounded-full {{ $child->active() ? 'bg-red' : 'bg-base-600 group-hover:bg-base-400' }} transition-colors duration-200">
                                                        </div>
                                                    @endif
                                                    {{ __($child->name) }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </li>

                <!-- Bottom section with visual card -->
                <li class="mt-auto">
                    <div class="mb-4 h-px bg-gradient-to-r from-transparent via-base-700 to-transparent"></div>
                    
                    <x-layout.user-profile-dropdown />
                </li>
            </ul>
        </nav>
    </div>
</div>
