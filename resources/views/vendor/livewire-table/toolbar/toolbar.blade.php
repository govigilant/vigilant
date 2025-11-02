@php($actions = $this->resolveActions())

<div class="bg-base-900 flex items-center flex-wrap gap-2 px-3 py-1.5 sm:px-4 sm:py-2 rounded-t-lg flex-1 border-b border-base-700 transition-all duration-200">
    @include('livewire-table::toolbar.loader')
    @includeWhen($this->canSearch(), 'livewire-table::toolbar.search')
    @includeWhen($this->canClearSearch(), 'livewire-table::toolbar.buttons.clear-search')
    @include('livewire-table::toolbar.notification')
    <div class="flex items-center gap-2 ml-auto">
        @includeWhen($this->useReordering, 'livewire-table::toolbar.buttons.reordering')
        @includeWhen($actions->isNotEmpty(), 'livewire-table::toolbar.dropdowns.actions')
        @include('livewire-table::toolbar.dropdowns.configuration')
    </div>
</div>
