<div
    class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-x-4 dark:bg-black px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
    <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden"
            x-on:click="sidebarOpen = true">
        <span class="sr-only">Open sidebar</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
        </svg>
    </button>

    <!-- Separator -->
    <div class="h-6 w-px bg-gray-900/10 lg:hidden" aria-hidden="true"></div>

    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
        <div class="flex-1"></div>
        <div class="flex items-center gap-x-4 lg:gap-x-6">
            <button type="button" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500">
                <span class="sr-only">View notifications</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                     aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                </svg>
            </button>

            <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-900/10" aria-hidden="true"></div>

            <div class="relative"
                 x-data="{ open: false }"
            >
                <button type="button" class="-m-1.5 flex items-center p-1.5"
                        x-on:click="open = !open"
                        aria-expanded="false" aria-haspopup="true">
                    <span class="sr-only">@lang('Open user menu')</span>
                    <span class="hidden lg:flex lg:items-center">
                <span class="ml-4 text-sm font-semibold leading-6 text-neutral-50"
                      aria-hidden="true">{{ auth()->user()->name ?? __('Menu') }}</span>
                <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd"/>
                </svg>
              </span>
                </button>

                <div
                    x-show="open"
                    x-on:click.outside="open = false"
                    x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none"
                    role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
{{--                    <a href="{{ route('teams.show', ['team' => auth()->user()->currentTeam->id]) }}"--}}
{{--                       class="block px-3 py-1 text-sm leading-6 text-gray-900"--}}
{{--                       role="menuitem"--}}
{{--                       tabindex="-1" id="user-menu-item-0">@lang('Your Team')</a>--}}
                    <a href="{{ route('profile.show') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900"
                       role="menuitem"
                       tabindex="-1" id="user-menu-item-0">@lang('Your profile')</a>
                    <form action="{{ route('logout') }}" method="POST">
                        {{ csrf_field() }}
                        <button type="submit" class="block px-3 py-1 text-sm leading-6 text-gray-900" role="menuitem"
                                tabindex="-1" id="user-menu-item-1">@lang('Sign out')
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
