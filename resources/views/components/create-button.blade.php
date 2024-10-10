@props(['model'])
@can('create', $model)
    <x-form.button {{ $attributes->merge(['class' => 'bg-blue hover:bg-blue-light']) }}>
        {{ $slot }}
    </x-form.button>
@else
    <x-form.button {{ $attributes->merge(['class' => 'bg-blue/70 hover:bg-blue/50 cursor-not-allowed has-tooltip'])->except(['href']) }} disabled>
        <span class="tooltip rounded shadow-lg bg-base-950 text-base-100 mt-8 prose px-2 py-1 text-xs leading-2">
           @lang('Your current plan does not allow to create this resource')
        </span>
        <span>
            {{ $slot }}
        </span>
    </x-form.button>
@endcan
