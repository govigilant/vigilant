<label class="relative group/search w-full sm:w-auto">
    <x-livewire-table::icon icon="magnifying-glass" class="absolute left-3 top-2.5 text-base-400 size-5 transition-colors duration-200 group-hover/search:text-base-300" />
    <input
        type="search"
        placeholder="@lang('Search all columns...')"
        @class([
            'pl-10 pr-4 py-2 rounded-lg border transition-all duration-200 w-full',
            'focus:outline-none focus:ring-2 focus:ring-red/50 focus:border-red focus:z-10',
            'bg-base-800 group-hover/search:bg-base-700 active:bg-base-700',
            'border-base-700 group-hover/search:border-base-600 focus:border-red',
            'text-base-100 placeholder:text-base-400',
        ])
        wire:model.live.debounce.500ms="globalSearch"
    >
</label>
