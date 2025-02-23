<div class="rounded-md bg-blue-light p-4" x-data="{ show: true }" x-show="show">
    <div class="flex">
        <div class="flex-shrink-0">
            <x-tni-info-circle-o class="h5 w-5 text-base-200" />
        </div>
        <div class="ml-3 flex-1 text-light/20">
            <div class="flex justify-between">
                <h3 class="text-sm font-medium flex-1">{{ $title }}</h3>
                <span x-on:click="show = false">
                    <x-tni-x-circle-o class="w-5 h-5 text-base-200 cursor-pointer" />
                </span>
            </div>
            @if (!blank($message ?? ''))
                <div class="mt-2 text-sm">
                    <p>{{ $message }}</p>
                </div>
            @endif
            {{ $slot }}
        </div>
    </div>
</div>
