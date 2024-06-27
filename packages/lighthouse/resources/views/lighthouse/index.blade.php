<x-app-layout>
    <x-slot name="header">
        <x-page-header :back="route('lighthouse')" title="Lighthouse Monitor - {{ $lighthouseSite->url }}">
            <x-form.button dusk="lighthouse-edit-button" class="bg-blue hover:bg-blue-light"
                           :href="route('uptime.monitor.edit', ['monitor' => $lighthouseSite])">
                @lang('Edit')
            </x-form.button>
        </x-page-header>
    </x-slot>

    <div class="flex gap-6">

        <dl class="grid grid-cols-4 grid-rows-2 gap-4">
            @foreach(['performance', 'accessibility', 'best_practices', 'seo'] as $category)
                @php
                    $color = 'text-red';

                    if ($lastResult !== null) {
                        $percentage = round($lastResult[$category] * 100);

                        $color = match(true) {
                            $percentage > 80 => 'text-green-light',
                            $percentage > 60 => 'text-orange-light',
                            default => 'text-red-light'
                        };

                    }

                @endphp
                <div class="text-base-50 bg-base-950 text-center p-4 rounded shadow">
                    <dt class="truncate text-sm font-medium text-base-100">{{ str_replace('_', ' ', ucfirst($category)) }}</dt>
                    <dd class="mt-1 text-xl font-semibold tracking-tight {{ $color ?? 'text-base-50' }}">{{ $lastResult === null ? '-' : $percentage . '%' }}</dd>
                </div>
            @endforeach

            @foreach(['7d' => 'Week', '30d' => 'Month', '90d' => '3 Months', '180d' => '6 Months'] as $timeframe => $label)
                <div class="text-base-50 bg-base-950 text-center p-4 rounded shadow">
                    <dt class="truncate text-sm font-medium text-base-100">{{ __($label) }}</dt>
                    <dd class="mt-1 text-xl font-semibold tracking-tight">
                        <x-lighthouse::average-difference :difference="$difference[$timeframe]"/>
                    </dd>
                </div>
            @endforeach
        </dl>

        <div class="flex-1">
            <livewire:lighthouse-categories-chart :data="['lighthouseSiteId' => $lighthouseSite->id]"/>
        </div>

    </div>

    <div class="mt-8 grid grid-cols-2 gap-12">
        @foreach($charts as $chart)
            <div>
                <h3 class="text-md font-bold leading-7 sm:truncate sm:text-xl sm:tracking-tight text-neutral-100">{{ $chart['title'] }}</h3>
                <p class="text-sm text-neutral-400 mb-4">
                    {{ $chart['description'] }}
                    <br/>
                    <a href="{{ $chart['link'] }}"
                       target="_blank">Learn more about the {{$chart['title']}} metric</a>.
                </p>

                <livewire:lighthouse-numeric-chart :audit="$chart['audit']"
                                                   :data="['lighthouseSiteId' => $lighthouseSite->id]"/>
            </div>
        @endforeach
    </div>


    <div class="my-4">
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">{{ __('Results') }}</h2>

        <livewire:lighthouse-results-table :siteId="$lighthouseSite->id"/>
    </div>

</x-app-layout>
