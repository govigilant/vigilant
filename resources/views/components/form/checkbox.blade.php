@props(['field', 'name', 'placeholder', 'description' => ''])
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="flex flex-col justify-center">
        <label for="{{ $field }}" class="block text-base font-semibold leading-6 text-base-50">@lang($name)</label>
        @if($description)
            <span class="text-base-400 text-sm mt-1">{{ $description }}</span>
        @endif
    </div>
    <div class="flex flex-col justify-center">
        <div class="flex items-center h-10">
            <input type="checkbox"
                   name="{{ $name }}"
                   id="{{ $field }}"
                   wire:model.live="{{ $field }}"
                   {{ $attributes }}
                   class="h-5 w-5 rounded border-base-700 bg-base-900 text-red focus:ring-red focus:ring-offset-base-850 cursor-pointer transition-colors duration-200"
            >
        </div>
        @error($field) <span class="text-red text-sm mt-1">{{ $message }}</span> @enderror
    </div>
</div>
