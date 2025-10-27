@if($column->isSortable())
    <button
       type="button"
       wire:click="sort(@js($column->code()))"
       class="flex items-center w-full gap-3 px-4 py-3 whitespace-nowrap cursor-pointer text-left text-base-100 hover:text-base-50 hover:bg-base-800/50 transition-all duration-200 rounded-lg group"
    >
        <span class="flex-1 font-semibold">{{ $column->label() }}</span>
        @if(! $this->isReordering())
            @if($this->sortColumn === $column->code())
                @if($this->sortDirection === 'asc')
                    <x-livewire-table::icon icon="chevron-up" class="size-4 text-red" />
                @else
                    <x-livewire-table::icon icon="chevron-down" class="size-4 text-red" />
                @endif
            @else
                <x-livewire-table::icon icon="chevron-up-down" class="size-4 opacity-50 group-hover:opacity-100 transition-opacity duration-200" />
            @endif
        @endif
    </button>
@else
    <span class="block px-4 py-3 whitespace-nowrap font-semibold text-base-100">{{ $column->label() }}</span>
@endif
