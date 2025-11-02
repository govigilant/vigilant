<div class="">

    <dl class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-frontend::stats-card :title="__('Expiry')">
            @if ($monitor->valid_to === null)
                <span class="text-red-light">@lang('Unknown')</span>
            @elseif ($monitor->valid_to->isPast())
                <span class="text-red-light">@lang('Expired :diff ago', ['diff' => $monitor->valid_to->longAbsoluteDiffForHumans()])</span>
            @else
                <span class="text-green">@lang('Expires in :diff', ['diff' => $monitor->valid_to->longAbsoluteDiffForHumans()])</span>
            @endif
        </x-frontend::stats-card>
        <x-frontend::stats-card :title="__('Valid from')">
            @if ($monitor->valid_from === null)
                <span class="text-red-light">@lang('Unknown')</span>
            @else
                <span class="text-base-200">{{ $monitor->valid_from->toDatetimeString() }}</span>
            @endif
        </x-frontend::stats-card>

        <x-frontend::stats-card :title="__('Valid to')">
            @if ($monitor->valid_to === null)
                <span class="text-red-light">@lang('Unknown')</span>
            @else
                <span class="text-base-200">{{ $monitor->valid_to->toDatetimeString() }}</span>
            @endif
        </x-frontend::stats-card>

        <x-frontend::stats-card :title="__('Issuer')">
            @if (data_get($monitor->data ?? [], 'issuer.CN') === null)
                <span class="text-red-light">@lang('Unknown')</span>
            @else
                <span class="text-base-200">{{ data_get($monitor->data, 'issuer.CN') }}</span>
            @endif
        </x-frontend::stats-card>
    </dl>
</div>
