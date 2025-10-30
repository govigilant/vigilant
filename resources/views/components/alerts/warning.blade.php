<div class="rounded-xl bg-gradient-to-r from-yellow to-yellow-light border border-yellow-light/30 p-5 shadow-lg shadow-yellow/20" 
     x-data="{ show: true }" 
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="flex">
        <div class="shrink-0">
            <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-white" />
        </div>
        <div class="ml-3 flex-1">
            <div class="flex justify-between">
                <h3 class="text-sm font-semibold text-white flex-1">{{ $title }}</h3>
                <button x-on:click="show = false" class="hover:bg-white/20 rounded-lg p-1 transition-colors duration-200">
                    <x-tni-x-circle-o class="w-5 h-5 text-white cursor-pointer" />
                </button>
            </div>
            @if (!blank($message ?? ''))
                <div class="mt-2 text-sm text-base-50">
                    <p>{{ $message }}</p>
                </div>
            @endif
            <div class="mt-2 text-sm text-base-50">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
