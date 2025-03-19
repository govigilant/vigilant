@php($tag = isset($href) ? 'a' : 'button')

<{{ $tag }}
    {{ $attributes->merge(['class' => 'rounded-md px-4 py-1.5 text-sm font-semibold text-white shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-light']) }}>
    {{ $slot }}
    </{{ $tag }}>
