@php($instance = app($condition['condition']))
@php($operators = $instance->operators())
@php($operands = $instance->operands())
<div class="flex flex-wrap items-center gap-3 mt-3 p-3 rounded-lg bg-base-800 border border-base-700" x-data="{ deleteHover: false }"
    :class="deleteHover ? 'ring-2 ring-red/50 border-red/50' : ''">
    <div class="flex items-center gap-2">
        <span class="block text-sm font-semibold leading-6 text-base-100">{{ $condition['condition']::$name }}</span>
        @if ($condition['condition']::info())
            <span class="text-base-400 cursor-help hover:text-base-300 transition-colors" title="{{ $condition['condition']::info() }}">ℹ️</span>
        @endif
    </div>
    @if (count($operands ?? []) > 0)
        <div>
            <select wire:model.live="children.{{ $path }}.operand"
                class="rounded-md border-0 py-1.5 pl-3 pr-10 text-base-100 bg-base-900 ring-1 ring-inset ring-base-700 focus:ring-2 focus:ring-inset focus:ring-red transition-all duration-200">
                @foreach ($operands as $value => $operand)
                    <option value="{{ $value }}">{{ $operand }}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if (count($operators ?? []) > 0)
        <div>
            <select wire:model.live="children.{{ $path }}.operator"
                class="rounded-md border-0 py-1.5 pl-3 pr-10 text-base-100 bg-base-900 ring-1 ring-inset ring-base-700 focus:ring-2 focus:ring-inset focus:ring-red transition-all duration-200">
                @foreach ($operators as $value => $operator)
                    <option value="{{ $value }}">{{ $operator }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <x-dynamic-component :component="$instance->type->view()" :condition="$instance" :path="$path" />

    <div class="flex-1"></div>

    <button type="button" 
        wire:click="deletePath('{{ $path }}')" 
        x-on:mouseover="deleteHover = true"
        x-on:mouseleave="deleteHover = false"
        class="cursor-pointer p-1 rounded-full hover:bg-red/20 transition-colors duration-200">
        @svg('tni-x-circle-o', 'w-6 h-6 text-red hover:text-red-light transition-colors')
    </button>

</div>
