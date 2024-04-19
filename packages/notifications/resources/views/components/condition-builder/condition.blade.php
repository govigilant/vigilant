@php($instance = app($condition['condition']))
@php($operators = $instance->getOperators())
@php($operands = $instance->getOperands())
<div class="flex items-center gap-4 mt-4">
    <span class="block text-sm font-medium leading-6 text-white">{{ $condition['condition']::$name }}</span>
    @if(count($operands ?? []) > 0)
        <div>
            <select
                wire:model.live="children.{{ $path }}.operand"
                class="rounded-md border-0 py-1 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                @foreach($operands as $operand)
                    <option value="{{ $operand }}">{{ $operand }}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if(count($operators ?? []) > 0)
        <div>
            <select
                wire:model.live="children.{{ $path }}.operator"
                class="rounded-md border-0 py-1 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                @foreach($operators as $operator)
                    <option value="{{ $operator }}">{{ $operator }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <x-dynamic-component :component="$instance->type->view()" :path="$path"/>
</div>
