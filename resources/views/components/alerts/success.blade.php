<div class="rounded-md bg-green-light p-4" x-data="{show: true}" x-show="show">
    <div class="flex">
        <div class="flex-shrink-0">
            <x-heroicon-o-check-circle class="h-5 w-5 text-white"/>
        </div>
        <div class="ml-3 flex-1">
            <div class="flex justify-between">
                <h3 class="text-sm font-medium text-white flex-1">{{ $title }}</h3>
                <span x-on:click="show = false">
                    <x-tni-x-circle-o class="w-5 h-5 text-white cursor-pointer"/>
                </span>
            </div>
            @if(!blank($message ?? ''))
                <div class="mt-2 text-sm text-base-100">
                    <p>{{ $message }}</p>
                </div>
            @endif
            {{ $slot }}
        </div>
    </div>
</div>
