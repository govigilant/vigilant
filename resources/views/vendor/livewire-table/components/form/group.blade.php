@props(['label'])

<div class="px-4 py-3 transition-all duration-200">
    <label class="flex flex-col gap-0.5">
        <span class="block uppercase text-xs font-bold whitespace-nowrap truncate text-base-400 transition-colors duration-200" title="{{ $label }}">{{ $label }}</span>
        {{ $slot }}
    </label>
</div>
