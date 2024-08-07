<div class="grid grid-cols-3 gap-4">
    <dl class="grid grid-cols-2 gap-4">
        <x-frontend::card>
            <dt class="truncate text-sm font-medium text-base-100">{{ $monitor->type->label() }}</dt>
            <dd class="mt-1 text-xl font-semibold tracking-tight text-base-50">{{ $monitor->settings['host'] ?? '' }}</dd>
        </x-frontend::card>

        <x-frontend::card>
            <dt class="truncate text-sm font-medium text-base-100">@lang('Last downtime')</dt>
            <dd class="mt-1 text-xl font-semibold tracking-tight text-base-50">{{ $lastDowntime === null ? __('Never') : teamTimezone($lastDowntime->end)->diffForHumans() }}</dd>
            @if ($lastDowntime !== null)
                <dd class="text-xs font-semibold tracking-tight text-base-50">@lang('For :time', ['time' =>  teamTimezone($lastDowntime->start)->longAbsoluteDiffForHumans(teamTimezone($lastDowntime->end))])</dd>
            @endif
        </x-frontend::card>

        <x-frontend::card>
            <dt class="truncate text-sm font-medium text-base-100">@lang('Uptime 30d')</dt>
            <dd class="mt-1 text-xl font-semibold tracking-tight text-base-50">{{ $uptime30d }}%</dd>
        </x-frontend::card>

        <x-frontend::card>
            <dt class="truncate text-sm font-medium text-base-100">@lang('Uptime 7d')</dt>
            <dd class="mt-1 text-xl font-semibold tracking-tight text-base-50">{{ $uptime7d }}%</dd>
        </x-frontend::card>
    </dl>

    <div class="col-span-2">
        <livewire:monitor-latency-chart :height="250" :data="['monitorId' => $monitor->id]"
                                        wire:key="latency-chart"/>
    </div>
</div>
