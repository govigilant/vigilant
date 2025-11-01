<div class="lg:hidden" x-data="{ open: false }">
    <button
        x-ref="trigger"
        class="group relative flex items-center justify-center rounded-lg px-4 py-2.5 bg-base-900/50 border border-base-800/50 text-base-300 transition-all duration-200 hover:bg-base-800/50 hover:border-base-700 hover:text-base-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red"
        type="button" x-on:click="open = !open">
        @svg('tni-menu-o', 'size-6 transition-colors duration-200')
    </button>
    
    <template x-teleport="body">
        <div
            x-show="open" 
            @click.away="open = false" 
            x-cloak
            class="fixed z-[9999] w-56 overflow-hidden rounded-lg bg-base-950 border border-base-800/50 shadow-xl"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            :style="`top: ${$refs.trigger?.getBoundingClientRect().bottom + 8}px; right: ${window.innerWidth - $refs.trigger?.getBoundingClientRect().right}px;`">
            <div class="py-1" role="menu">
                {{ $slot }}
            </div>
        </div>
    </template>
</div>
