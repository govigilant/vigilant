@props(['field', 'name', 'placeholder', 'description' => ''])
<div class="grid grid-cols-2">
    <div>
        <label for="{{ $field }}" class="block text-sm font-medium leading-6 text-white">@lang($name)</label>
        <span class="text-neutral-400 text-xs">{{ $description ?? '' }}</span>
    </div>
    <div class="mt-2">
        <input type="checkbox"
               name="{{ $name }}"
               id="{{ $field }}"
               wire:model.live="{{ $field }}"
               {{ $attributes }}
        >

        @error($field) <span class="text-red">{{ $message }}</span> @enderror
    </div>

</div>
