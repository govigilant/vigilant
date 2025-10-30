@props([
    'key' => '',
    'cloak' => true,
])

<div x-show="activeTab === '{{ $key }}'" 
     @if($cloak)x-cloak @endif
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-4"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     {{ $attributes->merge(['class' => 'space-y-6']) }}>
    {{ $slot }}
</div>
