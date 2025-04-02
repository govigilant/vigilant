<div>
    @if (!$inline)
        <x-slot name="header">
            <x-page-header :title="$updating ? 'Edit Lighthouse Monitor - ' . $lighthouseMonitor->url : 'Add Lighthouse Monitor'" :back="$updating ? route('lighthouse.index', ['monitor' => $lighthouseMonitor]) : route('lighthouse')">
            </x-page-header>
        </x-slot>
    @endif

    <form wire:submit="save">
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">
            @if (!$inline)
                <x-form.checkbox field="form.enabled" name="Enabled"
                    description="Enable or disable this lighthouse monitor" />
            @endif
            <x-form.text field="form.url" name="URL" description="Site URL" />

            <x-form.select field="form.interval" name="Interval"
                description="Choose how often this monitor should check the lighthouse scores">
                @foreach (config('lighthouse.intervals') as $interval => $label)
                    <option value="{{ $interval }}">@lang($label)</option>
                @endforeach
            </x-form.select>

            @if (!$inline)
                <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'" />
            @endif

        </div>
    </form>
</div>
