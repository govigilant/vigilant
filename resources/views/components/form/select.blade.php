@props(['field', 'name' => '', 'placeholder', 'description' => '', 'inline' => false])

@if(!$inline && !blank($name))
    <div class="grid grid-cols-2">
        <div>
            <label for="{{ $field }}" class="block text-sm font-medium leading-6 text-white">@lang($name)</label>
            <span class="text-neutral-400 text-xs">{{ $description ?? '' }}</span>
        </div>
        <div>
@endif
            <select id="{{ $field }}"
                    name="{{ $field }}"
                    wire:model.live="{{ $field }}"
                    {{ $attributes }}
                    class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-white bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
                {{ $slot }}
            </select>
            @error($field) <span class="text-red">{{ $message }}</span> @enderror
@if(!$inline && !blank($name))

        </div>

    </div>

@endif
