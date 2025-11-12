@props(['updating' => false])
<div>
    <x-slot name="header">
        <x-page-header :title="$updating
            ? __('Edit Healthcheck - :domain', ['domain' => $healthcheck->domain])
            : __('Add Healthcheck')" :back="route('healthchecks.index')">
        </x-page-header>
    </x-slot>

    <form wire:submit="save">
        <div class="max-w-7xl mx-auto">
            <x-card>
                <div class="flex flex-col gap-4">
                    <x-form.checkbox field="form.enabled" name="Enabled" description="Enable or disable this healthcheck" />

                    <x-form.text field="form.domain" name="Domain" description="Domain to monitor for health status" />

                    <x-form.select field="form.type" name="Type" description="Healthcheck type - Endpoint checks a specific URL path, Module checks built-in Laravel health checks">
                        @foreach (\Vigilant\Healthchecks\Enums\Type::cases() as $type)
                            <option value="{{ $type->value }}">{{ $type->name }}</option>
                        @endforeach
                    </x-form.select>

                    @if($form->type->value === 'endpoint')
                        <x-form.text field="form.endpoint" name="Endpoint" description="URL path to check (e.g., /health). Must return HTTP 200 status for a successful check." />
                    @endif

                    <x-form.select field="form.interval" name="Interval" description="Choose how often this healthcheck should run">
                        @foreach (config('healthchecks.intervals') as $interval => $label)
                            <option value="{{ $interval }}">@lang($label)</option>
                        @endforeach
                    </x-form.select>

                    <div class="flex justify-end gap-4 items-center">
                        <x-form.submit-button dusk="submit-button" wire:loading.attr="disabled" :submitText="$updating ? 'Save' : 'Create'" />
                    </div>
                </div>
            </x-card>
        </div>
    </form>
</div>
