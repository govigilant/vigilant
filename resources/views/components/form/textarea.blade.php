@props(['field', 'name' => '', 'placeholder' => '', 'description' => '', 'rows' => 4, 'live' => true, 'xRef' => null])
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @if(!blank($name))
    <div class="flex flex-col justify-center">
        <label for="{{ $field }}" class="block text-base font-semibold leading-6 text-base-50">@lang($name)</label>
        @if($description)
            <span class="text-base-400 text-sm mt-1">{{ $description }}</span>
        @endif
    </div>
    @endif
    <div class="flex flex-col justify-center">
        <div
            class="flex rounded-lg bg-base-900 ring-1 ring-inset ring-base-700 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red transition-all duration-200">
            <textarea
                name="{{ $name }}"
                id="{{ $field }}"
                rows="{{ $rows }}"
                @if($live) wire:model.blur="{{ $field }}" @else wire:model="{{ $field }}" @endif
                wire:loading.attr="disabled"
                placeholder="{{ $placeholder }}"
                @if($xRef) x-ref="{{ $xRef }}" @endif
                {{ $attributes->merge(['class' => 'flex-1 border-0 bg-transparent py-2.5 px-3 text-base-100 focus:ring-0 sm:text-sm sm:leading-6 disabled:bg-base-950 placeholder:text-base-500 resize-none font-mono']) }}></textarea>
        </div>

        @error($field) <span class="text-red text-sm mt-1">{{ $message }}</span> @enderror
    </div>
</div>
