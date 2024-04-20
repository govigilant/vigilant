@php($instance = app($condition['condition']))
@php($operators = $instance->operators())
@php($operands = $instance->operands())
<div class="flex items-center gap-4 mt-2 p-2 rounded-full" x-data="{ deleteHover: false }"
    :class="deleteHover ? 'bg-red-light/10' : ''">
    <span class="block text-sm font-medium leading-6 text-white">{{ $condition['condition']::$name }}</span>
    @if(count($operands ?? []) > 0)
        <div>
            <select
                wire:model.live="children.{{ $path }}.operand"
                class="rounded-md border-0 py-1 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                @foreach($operands as $value => $operand)
                    <option value="{{ $value }}">{{ $operand }}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if(count($operators ?? []) > 0)
        <div>
            <select
                wire:model.live="children.{{ $path }}.operator"
                class="rounded-md border-0 py-1 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                @foreach($operators as $value => $operator)
                    <option value="{{ $value }}">{{ $operator }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <x-dynamic-component :component="$instance->type->view()" :condition="$instance" :path="$path"/>

    <div class="flex-1"></div>

    <div class="cursor-pointer" wire:click="deletePath('{{ $path }}')" x-on:mouseover="deleteHover = true" x-on:mouseleave="deleteHover = false">
        @svg('tni-x-circle-o', 'w-6 h-6 text-red')
    </div>

</div>
