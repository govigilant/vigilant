<div>
    <x-slot name="header">
        <x-page-header title="Uptime Monitoring">
            <x-form.button :href="route('uptime.monitor.create')">
                @lang('Add Uptime Monitor')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <x-listing.status.wrapper>
        @foreach($monitors as $monitor)
            <x-listing.status.item :title="$monitor->name" :url="route('uptime.monitor.edit', ['monitor' => $monitor])">

                <x-slot:subtitle>
                    {{ $monitor->type->label() }} Monitor
                </x-slot:subtitle>


                <div class="flex gap-4">

                <div>
                    <livewire:monitor-latency-chart :monitorId="$monitor->id"/>
                </div>

                <div>
                    test
                </div>

                <div>
                    test2
                </div>

                </div>


{{--                Laaste check hier? --}}

{{--                Latency grafiekje --}}

{{--                Laatste downtime --}}


            </x-listing.status.item>
        @endforeach
    </x-listing.status.wrapper>

    {{ $monitors->links() }}

</div>
