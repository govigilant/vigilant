<div class="lg:hidden" x-data="{ open: false }">
    <button
        class="rounded-md bg-blue py-2 px-4 border border-transparent text-center text-sm text-white transition-all hover:bg-blue-light active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2"
        type="button" x-on:click="open = !open">
        @svg('tni-menu-o', 'size-5 text-base-200')
    </button>
    <ul role="menu"
        class="absolute z-40 min-w-[180px] overflow-auto rounded-lg bg-black py-1 shadow-lg shadow-xs focus:outline-hidden"
        x-show="open" @click.away="open = false" x-cloak style="right: 30px;">
        {{ $slot }}
    </ul>
</div>
