<div {{ $attributes->merge(['class' => 'mt-6 -mb-4']) }}>
    <h3 class="text-white text-xl">{{ $slot }}</h3>
    <x-section-border/>
</div>
