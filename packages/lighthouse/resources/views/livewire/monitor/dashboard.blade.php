<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <dl class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach (['performance', 'accessibility', 'best_practices', 'seo'] as $category)
            @php
                $color = 'text-red';

                if ($lastResult !== null) {
                    $percentage = round($lastResult[$category] * 100);

                    $color = match (true) {
                        $percentage > 80 => 'text-green-light',
                        $percentage > 60 => 'text-orange-light',
                        default => 'text-red-light',
                    };
                }

            @endphp
            <x-frontend::stats-card :title="__(str_replace('_', ' ', ucfirst($category)))">
                {{ $lastResult === null ? __('-') : $percentage . '%' }}
            </x-frontend::stats-card>
        @endforeach

        @foreach (['7d' => 'Week', '30d' => 'Month', '90d' => '3 Months', '180d' => '6 Months'] as $timeframe => $label)
            <x-frontend::stats-card :title="__($label)">
                <x-lighthouse::average-difference :difference="$difference[$timeframe]" />
            </x-frontend::stats-card>
        @endforeach
    </dl>

    <div class="flex-1">
        <livewire:lighthouse-categories-chart :data="['lighthouseMonitorId' => $lighthouseMonitor->id]" />
    </div>

</div>
