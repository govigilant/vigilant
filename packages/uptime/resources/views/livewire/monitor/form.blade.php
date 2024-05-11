<div>
    <x-slot name="header">
        <x-page-header :title="$updating ? 'Edit Uptime Monitor - ' . $monitor->name : 'Add Uptime Monitor'"
                       :back="route('uptime')">
        </x-page-header>
    </x-slot>

    <form wire:submit="save">
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">

            <x-form.text
                field="form.name"
                name="Name"
                description="Friendly name for this monitor"
            />

            <x-form.select
                field="form.type"
                name="Monitor Type"
                description="Choose how this monitor should check if the service is up"
            >
                @foreach(\Vigilant\Uptime\Enums\Type::cases() as $type)
                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                @endforeach
            </x-form.select>

            @if ($form->type === \Vigilant\Uptime\Enums\Type::Http->value)
                <x-form.text
                    field="form.settings.host"
                    name="Host"
                    description="HTTP Host"
                    placeholder="{{ config('app.url') }}"
                />
            @elseif ($form->type === \Vigilant\Uptime\Enums\Type::Ping->value)
                <x-form.text
                    field="form.settings.host"
                    name="Host"
                    description="Host or IP address of the service"
                    placeholder="{{ config('app.url') }} or 1.1.1.1"
                />

                <x-form.number
                    field="form.settings.port"
                    name="Port"
                    description="Port to check"
                />
            @endif

            <x-form.select
                field="form.interval"
                name="Interval"
                description="Choose how often this monitor should check the service"
            >
                <option value="* * * * *">@lang('Every minute')</option>
                <option value="*/2 * * * *">@lang('Every two minutes')</option>
                <option value="*/5 * * * *">@lang('Every five minutes')</option>
                <option value="0 * * * *">@lang('Hourly')</option>
            </x-form.select>

            <x-form.number
                field="form.retries"
                name="Retries"
                description="Amount of retries before marking the service as down"
            />

            <x-form.number
                field="form.timeout"
                name="Timeout"
                description="Timeout for connecting to the service"
            />

            @if(!$inline)
                <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'"/>
            @endif

        </div>
    </form>
</div>
