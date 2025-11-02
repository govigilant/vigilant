@props(['submit'])

<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-3 md:gap-8']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="mt-5 md:mt-0 md:col-span-2">
        <form wire:submit="{{ $submit }}">
            <div class="px-6 py-8 sm:p-8 bg-gradient-to-br from-base-850 to-base-900 border border-base-700 shadow-xl {{ isset($actions) ? 'sm:rounded-tl-xl sm:rounded-tr-xl' : 'sm:rounded-xl' }} relative overflow-hidden">
                <!-- Subtle gradient overlay -->
                <div class="absolute inset-0 bg-gradient-to-b from-base-800/10 to-transparent pointer-events-none"></div>
                
                <div class="grid grid-cols-6 gap-6 relative">
                    {{ $form }}
                </div>
            </div>

            @if (isset($actions))
                <div class="flex items-center justify-end gap-3 px-6 py-4 bg-base-900 border border-t-0 border-base-700 text-end shadow-xl sm:rounded-bl-xl sm:rounded-br-xl">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
