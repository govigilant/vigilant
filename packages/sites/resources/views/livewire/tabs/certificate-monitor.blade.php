<div>

    <div class="mb-4">
        <x-form.checkbox name="Enable Certificate Monitoring" description="Enable certificate monitoring for this site"
            field="enabled" dusk="certificate-tab-enabled"></x-form.checkbox>
    </div>

    @if ($enabled)
        <livewire:certificate-monitor-form :monitor="$this->monitor" :inline="true" />
    @endif

</div>
