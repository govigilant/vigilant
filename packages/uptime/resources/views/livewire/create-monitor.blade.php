<div>
    <x-slot name="header">
        <x-page-header title="Add Uptime Monitor" :back="route('uptime')">
        </x-page-header>
    </x-slot>


    <form wire:submit="save">
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">

            <x-form.text
                    field="name"
                    name="Name"
                    description="Friendly name for this monitor"
            />

            <x-form.select
                    field="type"
                    name="Monitor Type"
                    description="Choose how this monitor should check if the service is up"
            >
                @foreach(\Vigilant\Uptime\Enums\Type::cases() as $type)
                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                @endforeach
            </x-form.select>


            <x-form.submit-button/>

        </div>
    </form>
</div>
