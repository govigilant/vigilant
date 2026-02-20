@props(['field', 'name', 'description' => '', 'step' => '300'])
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="flex flex-col justify-center">
        <label for="{{ $field }}" class="block text-base font-semibold leading-6 text-base-50">@lang($name)</label>
        @if($description)
            <span class="text-base-400 text-sm mt-1">{{ $description }}</span>
        @endif
    </div>
    <div class="flex flex-col justify-center">
        <div
            class="flex rounded-lg bg-base-900 ring-1 ring-inset ring-base-700 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red transition-all duration-200">
            <input type="time"
                   name="{{ $name }}"
                   id="{{ $field }}"
                   wire:model.blur="{{ $field }}"
                   wire:loading.attr="disabled"
                   step="{{ $step }}"
                   {{ $attributes->merge(['class' => 'flex-1 border-0 bg-transparent py-2.5 px-3 text-base-100 focus:ring-0 sm:text-sm sm:leading-6 disabled:bg-base-950']) }}>
        </div>

        @error($field) <span class="text-red text-sm mt-1">{{ $message }}</span> @enderror
    </div>
</div>
