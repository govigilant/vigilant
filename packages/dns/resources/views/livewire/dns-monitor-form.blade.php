<div>
    <x-slot name="header">
        <x-page-header
            :title="$updating ? __('Edit DNS Monitor - :type :record', ['type' => $dnsMonitor->type->name, 'record' => $dnsMonitor->record])  : __('Add DNS Monitor')"
            :back="route('dns.index')">
        </x-page-header>
    </x-slot>

    <form wire:submit="save">
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">

            <x-form.select
                field="form.type"
                name="Type"
                description="DNS Record Type"
            >
                @foreach(\Vigilant\Dns\Enums\Type::cases() as $type)
                    <option value="{{ $type->value }}">{{ $type->name }}</option>
                @endforeach
            </x-form.select>

            <x-form.text
                field="form.record"
                name="Domain"
                description="Domain to monitor"
            />

            <x-form.text
                field="form.value"
                name="Value"
                description="Value that the record should resolve to"
            />

            <div class="flex justify-end gap-4">
                <x-form.button type="button" class="bg-blue" wire:click="resolve">@lang('Resolve value')</x-form.button>

                <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'"/>
            </div>
          </div>
    </form>
</div>
