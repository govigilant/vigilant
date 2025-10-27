@props(['disabled' => false])

<div
    class="flex rounded-lg bg-base-900 ring-1 ring-inset ring-base-700 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red transition-all duration-200">
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'flex-1 border-0 bg-transparent py-2.5 px-3 text-base-100 focus:ring-0 sm:text-sm sm:leading-6 placeholder:text-base-500']) !!}>
</div>
