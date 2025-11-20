<div class="bg-base-950 py-4 px-2 rounded-md border border-base-800">
    <div class="flex justify-between items-start mb-3">
        @if (count($availableKeys))
            <div class="ml-3 space-y-2 flex-1">
                <div class="flex flex-wrap gap-2 items-center">
                    @foreach ($availableKeys as $key)
                        <button wire:click="setMetricKey('{{ $key }}')" wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed" @class([
                                'px-3 py-1 text-xs font-medium rounded-full transition-colors duration-200 cursor-pointer relative',
                                'bg-blue text-white' => $selectedKey === $key,
                                'bg-base-800 text-base-200 hover:bg-base-700' => $selectedKey !== $key,
                            ])>
                            {{ $key }}
                        </button>
                    @endforeach
                </div>
            </div>
        @else
            <div class="flex-1 ml-3">
                <p class="text-sm text-base-400">{{ __('No metrics available') }}</p>
            </div>
        @endif

        <div class="mr-2 relative" x-data="{ open: false }">
            <button @click="open = !open" @click.away="open = false"
                class="px-3 py-1.5 text-xs font-medium rounded-md bg-base-800 text-base-200 hover:bg-base-700 transition-colors duration-200 flex items-center gap-2"
                wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed">
                <span>{{ $dateRangeOptions[$dateRange] }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-base-900 border border-base-800 z-10"
                style="display: none;">
                <div class="py-1">
                    @foreach ($dateRangeOptions as $key => $label)
                        <button wire:click="setDateRange('{{ $key }}')" @click="open = false"
                            @class([
                                'block w-full text-left px-4 py-2 text-sm transition-colors duration-200',
                                'bg-blue text-white' => $dateRange === $key,
                                'text-base-200 hover:bg-base-800' => $dateRange !== $key,
                            ])>
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

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
