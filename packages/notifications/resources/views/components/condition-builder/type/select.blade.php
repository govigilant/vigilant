<div>
    <select
        wire:model.live="children.{{ $path }}.value"
        class="rounded-md border-0 py-1.5 pl-3 pr-10 text-base-100 bg-base-900 ring-1 ring-inset ring-base-700 focus:ring-2 focus:ring-inset focus:ring-red transition-all duration-200">
        @foreach($condition->options() as $value => $name)
            <option value="{{ $value }}">{{ $name }}</option>
        @endforeach
    </select>
</div>
