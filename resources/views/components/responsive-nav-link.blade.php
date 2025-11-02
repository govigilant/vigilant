@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-indigo text-start text-base font-medium text-indigo-light bg-base-900 focus:outline-hidden focus:text-indigo-light focus:bg-base-850 focus:border-indigo transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-base-400 hover:text-base-200 hover:bg-base-900 hover:border-base-700 focus:outline-hidden focus:text-base-200 focus:bg-base-900 focus:border-base-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
