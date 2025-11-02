@php
$tag = $attributes->has('href') ? 'a' : 'button';
$typeAttr = $tag === 'button' ? 'type="button"' : '';
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => 'group flex items-center gap-3 w-full px-4 py-2.5 text-sm font-medium text-base-300 hover:text-base-100 hover:bg-base-900/50 transition-all duration-200 border-b border-base-800/30 last:border-b-0']) }} {!! $typeAttr !!}>
    {{ $slot }}
</{{ $tag }}>
