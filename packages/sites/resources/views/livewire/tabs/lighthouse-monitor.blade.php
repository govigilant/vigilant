<div>

    <div class="mb-4">
        <x-form.checkbox
            name="Enable Lighthouse Monitoring"
            description="Enable lighthouse monitoring for this site"
            field="enabled"
            dusk="lighthouse-tab-enabled"
        ></x-form.checkbox>
    </div>

    @if($enabled)
        <livewire:lighthouse-site-form
            :site="$this->monitor"
            :inline="true"
        />
    @endif

</div>
