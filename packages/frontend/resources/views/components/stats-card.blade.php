<x-frontend::card>
    <div class="px-1">
        <dt class="truncate text-xs md:text-sm font-medium text-base-100">{{ $title }}</dt>
        <dd
            class="mt-1 text-sm sm:text-md md:text-lg lg:text-xl font-semibold tracking-tight text-base-50 overflow-hidden">
            {{ $slot }}
        </dd>
    </div>
</x-frontend::card>
