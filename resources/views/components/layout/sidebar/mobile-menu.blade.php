<div class="relative z-50 lg:hidden" role="dialog" aria-modal="true" x-cloak x-show="sidebarOpen">

    <div class="fixed inset-0 bg-base-950" x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

    <div class="fixed inset-0 flex">

        <div class="relative mr-16 flex w-full max-w-xs flex-1" x-show="sidebarOpen"
            x-on:click.outside="sidebarOpen = false" x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full">

            <div class="absolute left-full top-0 flex w-16 justify-center pt-5"
                x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <button type="button" class="-m-2.5 p-2.5" x-on:click="sidebarOpen = false">
                    <span class="sr-only">Close sidebar</span>
                    <svg class="h-6 w-6 text-base-50" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-base-black px-6 pb-4 ring-1 ring-base-700">
                <div class="flex h-16 shrink-0 items-center pt-4">
                    <a href="/">
                        <img class="h-16 w-auto" src="{{ url('img/logo.svg') }}" alt="{{ config('app.name') }}">
                    </a>
                </div>
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <ul role="list" class="-mx-2 space-y-1">
                                @foreach (\Vigilant\Core\Facades\Navigation::items() as $item)
                                    @continue(!$item->shouldRender())
                                    <li>
                                        <a href="{{ $item->url }}" wire:navigate @class([
                                            'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                                            'bg-base-800 text-base-50' => $item->active(),
                                            'text-base-400 hover:text-base-50 hover:bg-base-800' => !$item->active(),
                                        ])>
                                            @if ($item->icon !== null)
                                                @svg($item->icon, 'h-6 w-6 shrink-0 ' . ($item->active() ? 'text-red' : ''))
                                            @endif
                                            {{ __($item->name) }}
                                        </a>

                                        @if ($item->hasChildren())
                                            <ul class="pl-12 border-l border-red">
                                                @foreach ($item->getChildren() as $child)
                                                    <li class="space-y-1">
                                                        <a href="{{ $child->url }}" wire:navigate
                                                            @class([
                                                                'group flex gap-x-3 rounded-md p-1 text-sm leading-6 font-semibold',
                                                                'text-base-50' => $child->active(),
                                                                'text-base-400 hover:text-base-50' => !$child->active(),
                                                            ])>
                                                            <span class="flex items-center gap-x-2">
                                                                @if ($child->icon !== null)
                                                                    @svg($child->icon, 'h-6 w-6 shrink-0 ' . ($child->active() ? 'text-red' : ''))
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
                        <li class="mt-auto">
                            <a href="{{ route('settings') }}" wire:navigate
                                class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-base-400 hover:bg-base-800 hover:text-base-50">
                                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Settings
                            </a>
                        </li>
                        <li class="-mx-2" x-data="{ open: false }">
                            <button type="button" class="group w-full flex items-center gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-base-400 hover:bg-base-800 hover:text-base-50" x-on:click="open = !open">
                                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <span class="flex-1 text-left">{{ auth()->user()->name ?? __('Menu') }}</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" x-cloak x-on:click.outside="open = false" class="mt-2 space-y-1">
                                <form action="{{ route('logout') }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit" class="group w-full flex items-center gap-x-3 rounded-md p-2 pl-11 text-sm font-semibold leading-6 text-base-400 hover:bg-base-800 hover:text-base-50">
                                        @lang('Sign out')
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
