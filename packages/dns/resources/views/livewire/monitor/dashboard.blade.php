<dl class="grid grid-cols-4 gap-4">
    <x-frontend::stats-card :title="__('Monitored Records')">
        {{ $count }}
    </x-frontend::stats-card>

    <x-frontend::stats-card :title="__('Last DNS Change')">
        @if ($lastChange === null)
            <x-frontend::mdash />
        @else
            @lang(':type record :record :time', [
                'type' => $lastChange->monitor->type->value,
                'record' => $lastChange->monitor->record,
                'time' => $lastChange->created_at->diffForHumans(),
            ])
        @endif
    </x-frontend::stats-card>
</dl>
