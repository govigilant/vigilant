<dl class="grid grid-cols-4 gap-4">
    <x-frontend::stats-card :title="__('URLs found')">
        {{ $total_url_count }}
    </x-frontend::stats-card>

    <x-frontend::stats-card :title="__('Issues')">
        {{ $issue_count ?? '0' }}
    </x-frontend::stats-card>

    <x-frontend::stats-card :title="__('Next Run')">
        {{ $nextRun }}
    </x-frontend::stats-card>

    <x-frontend::stats-card :title="__('Ignored URLs')">
        {{ $ignored_count ?? '0' }}
    </x-frontend::stats-card>
</dl>
