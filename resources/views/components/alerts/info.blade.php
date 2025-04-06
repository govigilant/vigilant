<div class="rounded-md bg-blue-light p-4" x-data="{ show: true }" x-show="show">
    <div class="flex">
        <div class="shrink-0">
            <x-tni-info-circle-o class="size-6 text-base-200" />
        </div>
        <div class="ml-3 flex-1 text-light/20">
            <div class="flex justify-between">
                <h3 class="text-sm font-bold flex-1 text-base-200">{{ $title }}</h3>
                <span x-on:click="show = false">
                    <x-tni-x-circle-o class="size-5 text-base-200 cursor-pointer" />
                </span>
            </div>
            @if (!blank($message ?? ''))
                <div class="mt-2 text-sm text-base-300">
                    <p>{{ $message }}</p>
                </div>
            @endif
            {{ $slot }}
        </div>
    </div>
</div>
