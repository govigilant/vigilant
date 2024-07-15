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
        <livewire:lighthouse-categories-chart :data="['monitorId' => $lighthouseMonitor->id]"/>
    </div>

</div>
