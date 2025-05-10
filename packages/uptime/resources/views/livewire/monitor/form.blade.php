<div>
    @if (!$inline)
        <x-slot name="header">
            <x-page-header :title="$updating ? 'Edit Uptime Monitor - ' . $monitor->name : 'Add Uptime Monitor'" :back="$updating ? route('uptime.monitor.view', ['monitor' => $monitor]) : route('uptime')">
            </x-page-header>
        </x-slot>
    @endif

    <form wire:submit="save">
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">

            @if (!$inline)
                <x-form.checkbox field="form.enabled" name="Enabled" description="Enable or disable this monitor" />
            @endif
            <x-form.text field="form.name" name="Name" description="Friendly name for this monitor" />

            <x-form.select field="form.type" name="Monitor Type"
                description="Choose how this monitor should check if the service is up">
                @foreach (\Vigilant\Uptime\Enums\Type::cases() as $type)
                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                @endforeach
            </x-form.select>

            @if ($form->type === \Vigilant\Uptime\Enums\Type::Http->value)
                <x-form.text field="form.settings.host" name="Host" description="HTTP Host"
                    placeholder="{{ config('app.url') }}" />
            @elseif ($form->type === \Vigilant\Uptime\Enums\Type::Ping->value)
                <x-form.text field="form.settings.host" name="Host" description="Host or IP address of the service"
                    placeholder="{{ config('app.url') }} or 1.1.1.1" />

                <x-form.number field="form.settings.port" name="Port" description="Port to check" />
            @endif

            <x-form.select field="form.interval" name="Interval"
                description="Choose how often this monitor should check the service">
                @foreach (config('uptime.intervals') as $interval => $label)
                    <option value="{{ $interval }}">@lang($label)</option>
                @endforeach
            </x-form.select>

            <x-form.number field="form.retries" name="Retries"
                description="Amount of retries before marking the service as down" />

            <x-form.number field="form.timeout" name="Timeout" description="Timeout for connecting to the service" />

            @if (!$inline)
                <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'" />
            @endif

        </div>
    </form>
</div>
