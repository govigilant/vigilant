<div>

    <div class="mb-4">
        <x-form.checkbox
            name="Enable Uptime Monitoring"
            description="Enable uptime monitoring for this site"
            field="enabled"
            dusk="uptime-tab-enabled"
        ></x-form.checkbox>
    </div>

    @if($enabled)
        <livewire:uptime-monitor-form
            :monitor="$this->monitor"
            :inline="true"
        />
    @endif

</div>
