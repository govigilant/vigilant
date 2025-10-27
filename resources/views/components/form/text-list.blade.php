@props(['field', 'items' => [], 'name' => '', 'placeholder', 'description' => '', 'live' => true])
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @if(!blank($name))
        <div class="flex flex-col justify-center">
            <label class="block text-base font-semibold leading-6 text-base-50">@lang($name)</label>
            @if($description)
                <span class="text-base-400 text-sm mt-1">{{ $description }}</span>
            @endif
        </div>
    @endif
    <div class="flex flex-col justify-center">
        <div class="space-y-3 mb-4">
            @foreach($items as $index => $item)
                <div
                    class="flex rounded-lg bg-base-900 ring-1 ring-inset ring-base-700 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red transition-all duration-200">
                    <input type="text"
                           name="{{ $name }}"
                           id="{{ $field . '.' . $index  }}"
                           @if($live) wire:model.blur="{{ $field . '.' . $index  }}"
                           @else wire:model="{{  $field . '.' . $index }}" @endif
                           wire:loading.attr="disabled"
                           {{ $attributes->merge(['class' => 'flex-1 border-0 bg-transparent py-2.5 px-3 text-base-100 focus:ring-0 sm:text-sm sm:leading-6 placeholder:text-base-500']) }}
                           placeholder="{{ $placeholder ?? '' }}">
                </div>

                @error($field . '.' . $index)<span class="text-red text-sm mt-1">@lang($message)</span>@enderror
            @endforeach
        </div>

        <x-form.button class="bg-blue hover:bg-blue-light inline-flex items-center gap-2" wire:click="addListItem('{{ $field }}')" type="button">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            @lang('Add')
        </x-form.button>

        <div>
            @error($field) <span class="text-red text-sm mt-1">@lang($message)</span> @enderror
        </div>
    </div>
</div>
