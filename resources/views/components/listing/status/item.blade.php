<li class="relative flex items-center space-x-4 py-4">
    <div class="min-w-0">
        <div class="flex items-center gap-x-3">
            <div class="flex-none rounded-full p-1 text-gray-500 bg-gray-100/10">

                <div class="h-2 w-2 rounded-full bg-green-light"></div>
            </div>
            <h2 class="min-w-0 text-sm font-semibold leading-6 text-white">
                <a href="{{ $url ?? '#' }}" class="flex gap-x-2">
                    <span class="truncate">{{ $title }}</span>
                </a>
            </h2>
        </div>
        <div class="mt-3 flex items-center gap-x-2.5 text-xs leading-5 text-gray-400">
            {{ $subtitle ?? '' }}
        </div>
    </div>
    <div class="flex-1">
        {{ $slot }}
    </div>
    <div>
        <svg class="h-5 w-5 flex-none text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd"
                  d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                  clip-rule="evenodd"/>
        </svg>
    </div>

</li>
