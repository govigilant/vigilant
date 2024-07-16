<div>
    @if(!$inline)
        <x-slot name="header">
            <x-page-header
                :title="$updating ? 'Edit Lighthouse Monitor - ' . $lighthouseMonitor->url : 'Add Lighthouse Monitor'"
                :back="route('lighthouse')">
            </x-page-header>
        </x-slot>
    @endif

    <form wire:submit="save">
        <div class="flex flex-col gap-4 max-w-7xl mx-auto">

            <x-form.text
                field="form.url"
                name="URL"
                description="Site URL"
            />

            <x-form.select
                field="form.interval"
                name="Interval"
                description="Choose how often this monitor should check the lighthouse scores"
            >
                <option value="0 * * * *">@lang('Hourly')</option>
                <option value="0 */3 * * *">@lang('Every three hours')</option>
                <option value="0 0 * * *">@lang('Daily')</option>
                <option value="0 0 0 * *">@lang('Weekly')</option>
            </x-form.select>

            @if(!$inline)
                <x-form.submit-button dusk="submit-button" :submitText="$updating ? 'Save' : 'Create'"/>
            @endif

        </div>
    </form>
</div>
