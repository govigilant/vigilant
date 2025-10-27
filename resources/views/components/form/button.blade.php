@php($tag = isset($href) ? 'a' : 'button')

<{{ $tag }}
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold text-base-100 shadow-md hover:shadow-lg hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-light cursor-pointer transition-all duration-200']) }}>
    {{ $slot }}
    </{{ $tag }}>
