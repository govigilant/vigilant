@php($columns = $this->resolveColumns())

<div class="overflow-x-auto">
<x-livewire-table::table x-data="{ selected: $wire.entangle('selected') }">
    <x-livewire-table::table.thead>
        <x-livewire-table::table.tr>
            @if($this->canSelect())
                <x-livewire-table::table.th class="px-3">
                    <x-livewire-table::form.checkbox wire:model.live="selectedPage" />
                </x-livewire-table::table.th>
            @endif
            @foreach($columns as $column)
                @continue(! in_array($column->code(), $this->columns))
                <x-livewire-table::table.th wire:key="{{ $column->code() }}">
                    {{ $column->renderHeader() }}
                </x-livewire-table::table.th>
            @endforeach
        </x-livewire-table::table.tr>
        @if($this->canSearch())
            <x-livewire-table::table.tr>
                @if($this->canSelect())
                    <x-livewire-table::table.td />
                @endif
                @foreach($columns as $column)
                    @continue(! in_array($column->code(), $this->columns))
                    <x-livewire-table::table.td wire:key="{{ $column->code() }}">
                        @if($column->isSearchable())
                            {{ $column->renderSearch() }}
                        @endif
                    </x-livewire-table::table.td>
                @endforeach
            </x-livewire-table::table.td>
        @endif
    </x-livewire-table::table.thead>

    <x-livewire-table::table.tbody>
        @if($this->deferLoading && ! $this->initialized)
            @for($i = 0; $i < $this->perPage(); $i++)
                <x-livewire-table::table.tr
                    wire:key="placeholder-{{ $i }}"
                    class="bg-base-900 odd:bg-base-900/50 hover:bg-base-800/80 transition-all duration-200 motion-safe:animate-pulse border-b border-base-700/50"
                >
                    @if($this->canSelect())
                        <x-livewire-table::table.td class="px-3">
                            <x-livewire-table::form.checkbox disabled />
                        </x-livewire-table::table.td>
                    @endif
                    @foreach($columns as $column)
                        @continue(! in_array($column->code(), $this->columns))
                        <td wire:key="{{ $column->code() }}">
                            <div class="px-4 py-3">
                                <span class="block w-full rounded-full min-w-8 h-2 my-2 bg-base-700 transition-all duration-200"></span>
                            </div>
                        </td>
                    @endforeach
                </x-livewire-table::table.tr>
            @endfor
        @else
            @forelse($paginator->items() as $item)
                <tr
                    x-data="@js(['item' => (string) $item->getKey()])"
                    x-bind:class="~selected.indexOf(item)
                        ? 'bg-red/10 hover:bg-red/20 transition-all duration-200 border-l-4 border-red shadow-sm shadow-red/20'
                        : 'bg-base-900 odd:bg-base-900/50 hover:bg-base-800/80 transition-all duration-200 border-b border-base-700/50'
                    "
                    wire:key="row-{{ $item->getKey() }}"

                    @if($this->isReordering())
                        draggable="true"
                        x-on:dragstart="e => e.dataTransfer.setData('key', item)"
                        x-on:dragover.prevent=""
                        x-on:drop="e => {
                            $wire.call(
                                'reorderItem',
                                e.dataTransfer.getData('key'),
                                item,
                                e.target.offsetHeight / 2 > e.offsetY
                            )
                        }"
                    @endif
                >
                    @if($this->canSelect())
                        <x-livewire-table::table.td class="px-3">
                            <x-livewire-table::form.checkbox x-ref="checkbox" wire:model="selected" value="{{ $item->getKey() }}" />
                        </x-livewire-table::table.td>
                    @endif
                    @foreach($columns as $column)
                        @continue(! in_array($column->code(), $this->columns))
                        <td
                            wire:key="{{ $column->code() }}"
                            @class([
                                'group/column relative' => true,
                                'select-none cursor-pointer' => $column->isClickable() || $this->isReordering(),
                            ])
                            @if($column->isClickable() && ! $this->isReordering())
                                @if(($link = $this->link($item)) !== null)
                                    @if($this->useNavigate)
                                        x-on:click.prevent="Livewire.navigate(@js($link))"
                                    @else
                                        x-on:click.prevent="window.location.href = @js($link)"
                                    @endif
                                @elseif($this->canSelect())
                                    x-on:click="$refs.checkbox.click()"
                                @endif
                            @endif
                        >
                            @includeWhen($column->isCopyable(), 'livewire-table::columns.buttons.copy')
                            <div x-ref="content">
                                {{ $column->render($item) }}
                            </div>
                        </td>
                    @endforeach
                </tr>
            @empty
                <x-livewire-table::table.tr class="bg-base-900/50 transition-all duration-200">
                    <x-livewire-table::table.td colspan="{{ $columns->count() + 1 }}" class="text-center py-12">
                        <x-livewire-table::table.message>
                            @lang('No results')
                        </x-livewire-table::table.message>
                    </x-livewire-table::table.td>
                </x-livewire-table::table.tr>
            @endforelse
        @endif
    </x-livewire-table::table.tbody>

    <x-livewire-table::table.tfoot>
        <x-livewire-table::table.tr>
            @if($this->canSelect())
                <x-livewire-table::table.th />
            @endif
            @foreach($columns as $column)
                @continue(! in_array($column->code(), $this->columns))
                <x-livewire-table::table.th wire:key="{{ $column->code() }}">
                    {{ $column->renderFooter() }}
                </x-livewire-table::table.th>
            @endforeach
        </x-livewire-table::table.tr>
    </x-livewire-table::table.tfoot>
</x-livewire-table::table>
</div>
