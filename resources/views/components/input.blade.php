@props(['disabled' => false])

<div
    class="flex rounded-md bg-white/5 ring-1 ring-inset ring-white/10 focus-within:ring-2 focus-within:ring-inset focus-within:ring-red">
    <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'flex-1 border-0 bg-transparent py-1.5 text-white focus:ring-0 sm:text-sm sm:leading-6']) !!}>
</div>
