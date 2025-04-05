<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <dl class="grid grid-cols-2 gap-4">
        <x-frontend::stats-card :title="$monitor->type->label()">
            {{ $monitor->settings['host'] ?? '' }}
        </x-frontend::stats-card>

        <x-frontend::stats-card :title="__('Last Downtime')">
            {{ $lastDowntime === null ? __('Never') : teamTimezone($lastDowntime->end)->diffForHumans() }}
            @if ($lastDowntime !== null)
                @lang('For :time', ['time' => teamTimezone($lastDowntime->start)->longAbsoluteDiffForHumans(teamTimezone($lastDowntime->end))])
            @endif
        </x-frontend::stats-card>

        <x-frontend::stats-card :title="__('Uptime last 30 days')">
            @if ($uptime30d === null)
                <x-frontend::mdash />
            @else
                {{ $uptime30d }}%
            @endif
        </x-frontend::stats-card>

        <x-frontend::stats-card :title="__('Uptime last 7 days')">
            @if ($uptime7d === null)
                <x-frontend::mdash />
            @else
                {{ $uptime7d }}%
            @endif
        </x-frontend::stats-card>
    </dl>

    <div class="md:col-span-2">
        <livewire:monitor-latency-chart :height="250" :data="['monitorId' => $monitor->id]" wire:key="latency-chart" />
    </div>
</div>
