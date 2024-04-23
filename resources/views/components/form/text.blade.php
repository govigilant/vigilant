@props(['field', 'name', 'placeholder', 'description' => ''])
<div class="grid grid-cols-2">
    <div>
        <label for="{{ $field }}" class="block text-sm font-medium leading-6 text-white">@lang($name)</label>
        <span class="text-neutral-400 text-xs">{{ $description ?? '' }}</span>
    </div>
    <div class="mt-2">
        <div
            class="flex rounded-md bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
            <input type="text"
                   name="{{ $name }}"
                   id="{{ $field }}"
                   wire:model.live="{{ $field }}"
                   {{ $attributes->merge(['class' => 'flex-1 border-0 bg-transparent py-1.5 text-white focus:ring-0 sm:text-sm sm:leading-6']) }}
                   placeholder="{{ $placeholder ?? '' }}">
        </div>

        @error($field) <span class="text-red">{{ $message }}</span> @enderror
    </div>
</div>
