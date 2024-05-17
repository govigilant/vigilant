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

        <div class="flex flex-col gap-4">
            <dl class="flex gap-2">
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
                    <div class="text-base-50 bg-base-950 text-center p-6 rounded shadow">
                        <dt class="truncate text-sm font-medium text-base-100">{{ str_replace('_', ' ', ucfirst($category)) }}</dt>
                        <dd class="mt-1 text-xl font-semibold tracking-tight {{ $color ?? 'text-base-50' }}">{{ $lastResult === null ? '-' : $percentage . '%' }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="text-base-50 bg-base-950 p-6 rounded shadow">
                <h2 class="text-xl mb-1">@lang('Average difference over time')</h2>
                <div>
                    <span>@lang('7 days')</span>
                    <span>{{ $difference['7d'] === null ? '-' : round($difference['7d']->averageDifference()) . '%' }}</span>
                </div>
                <div>
                    <span>@lang('30 days')</span>
                    <span>{{ $difference['30d'] === null ? '-' : round($difference['30d']->averageDifference()) . '%' }}</span>
                </div>
                <div>
                    <span>@lang('90 days')</span>
                    <span>{{ $difference['90d'] === null ? '-' : round($difference['90d']->averageDifference()) . '%' }}</span>
                </div>
            </div>
        </div>

        <div class="flex-1">
            <livewire:lighthouse-categories-chart :data="['lighthouseSiteId' => $lighthouseSite->id]"/>
        </div>

    </div>

    <div class="mt-4">
        <h2 class="text-xl font-bold leading-7 sm:truncate sm:text-2xl sm:tracking-tight text-neutral-100 mb-2">{{ __('Results') }}</h2>

        <livewire:lighthouse-results-table :siteId="$lighthouseSite->id"/>
    </div>


</x-app-layout>
