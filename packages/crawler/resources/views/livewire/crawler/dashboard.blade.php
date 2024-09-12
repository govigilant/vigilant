<dl class="grid grid-cols-4 gap-4">
    <x-frontend::card>
        <dt class="truncate text-sm font-medium text-base-100">@lang('URLs found')</dt>
        <dd class="mt-1 text-xl font-semibold tracking-tight text-base-50">{{ $total_url_count }}</dd>
    </x-frontend::card>

    <x-frontend::card>
        <dt class="truncate text-sm font-medium text-base-100">@lang('Issues')</dt>
        <dd class="mt-1 text-xl font-semibold tracking-tight text-base-50">{{ $issue_count ?? '0' }}</dd>
    </x-frontend::card>

    <x-frontend::card>
        <dt class="truncate text-sm font-medium text-base-100">@lang('Next Run')</dt>
        <dd class="mt-1 text-xl font-semibold tracking-tight text-base-50">{{ $nextRun }}</dd>
    </x-frontend::card>
</dl>
