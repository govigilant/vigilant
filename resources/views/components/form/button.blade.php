@php($tag = isset($href) ? 'a' : 'button')

<{{ $tag }}
    {{ $attributes->merge(['class' => 'inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3 text-sm font-semibold text-base-100 shadow-lg hover:shadow-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-light cursor-pointer transition-all duration-300']) }}>
    {{ $slot }}
    </{{ $tag }}>
