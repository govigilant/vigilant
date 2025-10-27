@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo text-sm font-medium leading-5 text-base-50 focus:outline-hidden focus:border-indigo-light transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-base-400 hover:text-base-200 hover:border-base-700 focus:outline-hidden focus:text-base-200 focus:border-base-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
