<div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-frontend::stats-card :title="__('Domain')">
            {{ $healthcheck->domain }}
        </x-frontend::stats-card>

        <x-frontend::stats-card :title="__('Last Check')">
            {{ $healthcheck->last_check_at ? $healthcheck->last_check_at->diffForHumans() : __('Never') }}
        </x-frontend::stats-card>

        <x-frontend::stats-card :title="__('Status')">
            @if($healthcheck->status === \Vigilant\Healthchecks\Enums\Status::Healthy)
                <span class="text-green-light">{{ __('Healthy') }}</span>
            @elseif($healthcheck->status === \Vigilant\Healthchecks\Enums\Status::Warning)
                <span class="text-orange">{{ __('Warning') }}</span>
            @elseif($healthcheck->status === \Vigilant\Healthchecks\Enums\Status::Unhealthy)
                <span class="text-red">{{ __('Unhealthy') }}</span>
            @else
                <span class="text-neutral-400">{{ __('Unknown') }}</span>
            @endif
        </x-frontend::stats-card>

        <x-frontend::stats-card :title="__('Interval')">
            {{ $healthcheck->interval }}s
        </x-frontend::stats-card>
    </div>

    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-semibold text-base-50 mb-3">{{ __('Metrics') }}</h3>
            <livewire:healthcheck-metric-chart :data="['healthcheckId' => $healthcheck->id]" wire:key="metric-chart-{{ $healthcheck->id }}" />
        </div>

        <div>
            <h3 class="text-lg font-semibold text-base-50 mb-3">{{ __('Recent Results') }}</h3>
            <livewire:healthcheck-result-table :healthcheckId="$healthcheck->id" wire:key="result-table-{{ $healthcheck->id }}" />
        </div>
    </div>
</div>
