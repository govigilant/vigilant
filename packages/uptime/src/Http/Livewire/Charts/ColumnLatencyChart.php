<?php

namespace Vigilant\Uptime\Http\Livewire\Charts;

use Illuminate\View\View;

class ColumnLatencyChart extends LatencyChart
{
    public int $height = 40;

    public function data(): array
    {
        $points = $this->points()->pluck('total_time');

        return [
            'type' => 'line',
            'data' => [
                'labels' => $points,
                'datasets' => [
                    [
                        'label' => 'Latency',
                        'data' => $points,
                        'pointRadius' => 0,
                        'pointHoverRadius' => 0,
                        'borderWidth' => 2,
                        'borderColor' => '#337F1F',
                        'tension' => 0.4,
                    ],
                ],
            ],
            'options' => [
                'plugins' => [
                    'legend' => [
                        'display' => false,
                    ],
                    'tooltip' => [
                        'enabled' => false,
                    ],
                ],
                'scales' => [
                    'y' => [
                        'display' => false,
                    ],
                    'x' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }

    public function render(): View
    {
        /** @var view-string $view */
        $view = 'uptime::livewire.charts.column-latency-chart';

        return view($view, [
            'identifier' => $this->getIdentifier(),
            'height' => $this->height,
            'hasPoints' => $this->points()->isNotEmpty(),
        ]);
    }
}
