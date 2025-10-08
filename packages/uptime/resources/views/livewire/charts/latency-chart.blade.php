<div class="bg-base-950 py-4 px-2 rounded-md border border-base-800">
    @if ($availableCountries->count() > 1)
        <div class="ml-3 flex flex-wrap gap-2 items-center">
            @foreach ($availableCountries as $country)
                <button wire:click="selectCountry('{{ $country }}')" wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed" @class([
                        'px-3 py-1 text-xs font-medium rounded-full transition-colors duration-200 cursor-pointer',
                        'bg-blue text-white' => $selectedCountry === $country,
                        'bg-base-800 text-base-200 hover:bg-base-700' =>
                            $selectedCountry !== $country,
                    ])>
                    {{ strtoupper($country) }}
                </button>
            @endforeach
        </div>
    @endif

    <div wire:init="loadChart" x-data="{ show: false, loading: true }">
        <div style="height: {{ $height }}px;" wire:ignore x-init="() => {
            Livewire.on('{{ $identifier }}-update-chart', params => {
                config = params[0]

                show = config.data.labels.length > 0
                loading = false

                let chart = Chart.getChart('{{ $identifier }}');

                config.options.plugins.tooltip.callbacks = {
                    label: function(context) {
                        let unit = context.dataset.unit || '';

                        return context.dataset.label + ': ' + context.formattedValue + ' ' + unit;
                    }
                };

                if (typeof chart === 'undefined') {
                    chart = new Chart(document.getElementById('{{ $identifier }}'), config);
                } else {
                    chart.reset();
                    chart.type = config.type;
                    chart.data = config.data;
                    chart.options = config.options;
                    chart.update();
                }
            });
        }">
            <canvas wire:ignore id="{{ $identifier }}"></canvas>
        </div>
    </div>
</div>
