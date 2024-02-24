<div>
    <x-slot name="header">
        <x-page-header title="Uptime Monitoring">
            <x-form.button :href="route('uptime.monitor.create')">
                @lang('Add Uptime Monitor')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <livewire:uptime-monitor-table/>

{{--    <x-listing.status.wrapper class="grid grid-cols-5">--}}

{{--        <x-listing.status.header title="Monitor">--}}
{{--            <div class="flex gap-4">--}}

{{--                <div>@lang('Latency')</div>--}}
{{--                <div>@lang('Uptime')</div>--}}
{{--                <div>@lang('Last Downtime')</div>--}}

{{--            </div>--}}
{{--        </x-listing.status.header>--}}

{{--        @foreach($monitors as $monitor)--}}
{{--            <x-listing.status.item :title="$monitor->name" :url="route('uptime.monitor.edit', ['monitor' => $monitor])">--}}

{{--                <x-slot:subtitle>--}}
{{--                    {{ $monitor->type->label() }} Monitor--}}
{{--                </x-slot:subtitle>--}}


{{--                <div class="flex gap-4">--}}

{{--                    @php($latency = $monitor->aggregatedResults()->orderByDesc('created_at')->first()?->total_time ?? null)--}}

{{--                    @if ($latency !== null)--}}
{{--                        <div>--}}
{{--                            <span class="relative top-1 text-sm text-green-light">{{ $latency }} ms</span>--}}
{{--                            <livewire:monitor-latency-chart :monitorId="$monitor->id"/>--}}
{{--                        </div>--}}
{{--                    @endif--}}

{{--                    <div>--}}
{{--                        test--}}
{{--                    </div>--}}

{{--                    <div>--}}
{{--                        test2--}}
{{--                    </div>--}}

{{--                </div>--}}


{{--                --}}{{--                Laaste check hier? --}}

{{--                --}}{{--                Latency grafiekje --}}

{{--                --}}{{--                Laatste downtime --}}


{{--            </x-listing.status.item>--}}
{{--        @endforeach--}}
{{--    </x-listing.status.wrapper>--}}

{{--    {{ $monitors->links() }}--}}

</div>
