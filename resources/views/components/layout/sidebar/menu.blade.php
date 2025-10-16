<div class="hidden h-full lg:inset-y-0 lg:z-40 lg:flex lg:w-72 lg:flex-col rounded-br-2xl">
    <div class="flex grow flex-col gap-y-5 overflow-y-auto dark:bg-black px-6 pb-4">
        <div class="flex h-16 shrink-0 items-center pt-4">
            <a href="/">
                <img class="h-14 w-auto" src="{{ url('img/logo.svg') }}" alt="{{ config('app.name') }}">
            </a>
        </div>
        <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                <li>
                    <ul role="list" class="-mx-2 space-y-1">
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
                                        'group flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                                        'bg-base-900 text-base-50' => $item->active() || $activeChild,
                                        'text-base-300 hover:text-white hover:bg-base-900' => !$item->active(),
                                    ])>
                                    @if ($item->icon !== null)
                                        @svg($item->icon, 'h-6 w-6 shrink-0 ' . ($item->active() || $activeChild ? 'text-red' : 'text-base-400'))
                                    @endif
                                    <span class="flex-1">{{ __($item->name) }}</span>

                                    @if ($item->hasChildren() || $activeChild)
                                        <span x-show="!showChildren" x-cloak>@svg('heroicon-o-chevron-up', 'h-5 w-5')</span>
                                        <span x-show="showChildren" x-cloak>@svg('heroicon-o-chevron-down', 'h-5 w-5')</span>
                                    @endif

                                </a>

                                @if ($item->hasChildren())
                                    <ul class="pl-5" x-show="showChildren" x-cloak>
                                        @foreach ($item->getChildren() as $child)
                                            <li class="p-2 border-l border-red">
                                                <a href="{{ $child->url }}" wire:navigate
                                                    @class([
                                                        'group flex gap-x-3 rounded-md p-1 text-sm leading-6 font-semibold',
                                                        'text-white' => $child->active(),
                                                        'text-gray-500 hover:text-white' => !$child->active(),
                                                    ])>
                                                    <span class="flex items-center gap-x-2">
                                                        @if ($child->icon !== null)
                                                            @svg($child->icon, 'h-6 w-6 shrink-0 ' . ($child->active() ? 'text-red' : 'text-base-400'))
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
                    <ul role="list" class="-mx-2 space-y-1">
                        <li>
                            <a href="{{ route('settings') }}" wire:navigate @class([
                                'group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-400 hover:bg-gray-800 hover:text-white',
                                'bg-gray-800 text-white' => Route::currentRouteName() === 'settings',
                            ])>
                                <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" @class([
                                    'h-6 w-6 shrink-0',
                                    'text-red' => Route::currentRouteName() === 'settings',
                                ])
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                @lang('Settings')
                            </a>
                        </li>
                        <li x-data="{ open: false }">
                            <button type="button"
                                class="group w-full flex items-center gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-400 hover:bg-gray-800 hover:text-white"
                                x-on:click="open = !open">
                                <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <span class="flex-1 text-left">{{ auth()->user()->name ?? __('Menu') }}</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" x-cloak x-on:click.outside="open = false" class="mt-2 space-y-1">
                                <form action="{{ route('logout') }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit"
                                        class="group w-full flex items-center gap-x-3 rounded-md p-2 pl-11 text-sm font-semibold leading-6 text-gray-400 hover:bg-gray-800 hover:text-white">
                                        @lang('Sign out')
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
