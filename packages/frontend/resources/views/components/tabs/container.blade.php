@props([
    'tabs' => [],
    'activeTab' => '',
])

<div x-data="{ activeTab: '{{ $activeTab }}' }" {{ $attributes }}>
    {{ $slot }}
</div>
