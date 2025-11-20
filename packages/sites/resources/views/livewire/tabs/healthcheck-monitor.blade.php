<div>

    <div class="mb-4">
        <x-form.checkbox
            name="Enable Healthcheck Monitoring"
            description="Enable healthcheck monitoring for this site"
            field="enabled"
            dusk="healthcheck-tab-enabled"
        ></x-form.checkbox>
    </div>

    @if($enabled)
        <livewire:healthcheck-form
            :healthcheck="$this->healthcheck"
            :inline="true"
        />
    @endif

</div>
