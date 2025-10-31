<div
    class="flex flex-col gap-3 relative"
    @if($this->deferLoading) wire:init="init" @endif
    @if(strlen($polling = $this->polling()) > 0) wire:poll.{{ $polling }} @endif
>
    <div class="bg-base-850 rounded-lg shadow-lg flex flex-col border border-base-700 transition-all duration-200">
        @include('livewire-table::toolbar.toolbar')
        <div class="flex-1 overflow-x-auto overflow-y-auto max-h-179 rounded-b-lg">
            @include('livewire-table::table.table')
        </div>
    </div>
    {{ $paginator->links('livewire-table::pagination.pagination') }}
</div>
