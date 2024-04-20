<div>
    <select
        wire:model.live="children.{{ $path }}.value"
        class="rounded-md border-0 py-1 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
        @foreach($condition->options() as $value => $name)
            <option value="{{ $value }}">{{ $name }}</option>
        @endforeach
    </select>
</div>
