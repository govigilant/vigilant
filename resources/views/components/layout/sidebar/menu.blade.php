<div class="hidden h-full lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
    <div class="flex grow flex-col gap-y-5 overflow-y-auto dark:bg-black px-6 pb-4">
        <div class="flex h-16 shrink-0 items-center pt-4">
            <a href="/">
                <img class="h-14 w-auto" src="{{ url('img/logo.svg') }}"
                     alt="{{ config('app.name') }}">
            </a>
        </div>
        <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                <li>
                    <ul role="list" class="-mx-2 space-y-1">
                        @foreach(\Vigilant\Core\Facades\Navigation::items() as $item)
                            @php
                                $activeChild = false;
                                if($item->hasChildren()) {
                                    foreach($item->getChildren() as $child) {
                                        if ($child->active()) {
                                            $activeChild = true;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <li x-data="{ showChildren: {{ $item->active() || $activeChild ? 'true' : 'false' }} }">
                                <a href="{{ $item->url }}"
                                   wire:navigate
                                    @class([
                                         'group flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold' ,
                                         'bg-gray-800 text-white' => $item->active() || $activeChild,
                                         'text-gray-400 hover:text-white hover:bg-gray-800' => !$item->active(),
                                    ])
                                >
                                    @if($item->icon !== null)
                                        @svg($item->icon, 'h-6 w-6 shrink-0 ' . ($item->active() || $activeChild ?
                                        'text-red' : ''))
                                    @endif
                                    <span class="flex-1">{{ __($item->name)  }}</span>

                                    @if($item->hasChildren() || $activeChild)
                                        <span x-show="!showChildren"
                                              x-cloak>@svg('heroicon-o-chevron-up', 'h-5 w-5')</span>
                                        <span x-show="showChildren"
                                              x-cloak>@svg('heroicon-o-chevron-down', 'h-5 w-5')</span>
                                    @endif

                                </a>

                                @if($item->hasChildren())
                                    <ul class="pl-5" x-show="showChildren">
                                        @foreach($item->getChildren() as $child)
                                            <li class="p-2 border-l border-base-600">
                                                <a href="{{ $child->url }}"
                                                   wire:navigate
                                                    @class([
                                                         'group flex gap-x-3 rounded-md p-1 text-sm leading-6 font-semibold' ,
                                                         'text-white' => $child->active(),
                                                         'text-gray-500 hover:text-white' => !$child->active(),
                                                    ])
                                                >
                                                    {{ __($child->name)  }}
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
                    <a href="#"
                       class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-400 hover:bg-gray-800 hover:text-white">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Settings
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
