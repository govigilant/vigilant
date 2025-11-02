@props(['field', 'name' => '', 'placeholder', 'description' => '', 'inline' => false])

@if(!$inline && !blank($name))
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex flex-col justify-center">
            <label for="{{ $field }}" class="block text-base font-semibold leading-6 text-base-50">@lang($name)</label>
            @if($description)
                <span class="text-base-400 text-sm mt-1">{{ $description }}</span>
            @endif
        </div>
        <div class="flex flex-col justify-center">
@endif
            <select id="{{ $field }}"
                    name="{{ $field }}"
                    wire:model.live="{{ $field }}"
                    {{ $attributes }}
                    class="block w-full rounded-lg border-0 py-2.5 px-3 text-base-100 bg-base-900 ring-1 ring-inset ring-base-700 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red disabled:bg-base-950 transition-all duration-200">
                {{ $slot }}
            </select>
            @error($field) <span class="text-red text-sm mt-1">{{ $message }}</span> @enderror
@if(!$inline && !blank($name))

        </div>

    </div>

@endif
