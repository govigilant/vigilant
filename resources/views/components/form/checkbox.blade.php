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
            <!-- Toggle Switch -->
            <button type="button"
                    role="switch"
                    x-data="{ enabled: $wire.entangle('{{ $field }}').live }"
                    x-on:click="enabled = !enabled"
                    :aria-checked="enabled.toString()"
                    :class="enabled ? 'bg-gradient-to-r from-red to-orange' : 'bg-base-700'"
                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red focus:ring-offset-2 focus:ring-offset-base-900">
                <span class="sr-only">{{ $name }}</span>
                <span :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-base-50 shadow-lg ring-0 transition duration-200 ease-in-out"></span>
            </button>
        </div>
        @error($field) <span class="text-red text-sm mt-1">{{ $message }}</span> @enderror
    </div>
</div>
