<div>
    <x-slot name="header">
        <x-page-header title="Uptime Monitoring">
            <x-form.button :href="route('uptime.create')">
                @lang('Add Uptime Monitor')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <x-listing.status.wrapper>
        @foreach($checkers as $checker)
            <x-listing.status.item :title="$checker->name">
                <p class="truncate">Deploys from GitHub</p>
                <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 flex-none fill-gray-300">
                    <circle cx="1" cy="1" r="1"/>
                </svg>
                <p class="whitespace-nowrap">Initiated 1m 32s ago</p>
            </x-listing.status.item>
        @endforeach
    </x-listing.status.wrapper>

    {{ $checkers->links() }}

</div>
