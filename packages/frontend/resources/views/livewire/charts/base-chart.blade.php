<div wire:init="loadChart" x-data="{show: false, loading: true}">

    <div
        style="height: {{ $height }}px;"
        wire:ignore
        x-init="() => {
            Livewire.on('{{ $identifier }}-update-chart', params => {
                config = params[0]

                show = config.data.labels.length > 0
                loading = false

                let chart = Chart.getChart('{{ $identifier }}');

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
