@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-base-100']) }}>
    {{ $value ?? $slot }}
</label>
