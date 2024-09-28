@props(['model'])
@can('create', $model)
    <x-form.button {{ $attributes->merge(['class' => 'bg-blue hover:bg-blue-light']) }}>
        {{ $slot }}
    </x-form.button>
@else
    <x-form.button {{ $attributes->merge(['class' => 'bg-blue/70 hover:bg-blue/50 cursor-not-allowed'])->except(['href']) }} disabled>
        {{ $slot }}
    </x-form.button>
@endcan

