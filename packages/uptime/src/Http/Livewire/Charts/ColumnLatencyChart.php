<?php

namespace Vigilant\Uptime\Http\Livewire\Charts;

use Illuminate\Support\Str;

class ColumnLatencyChart extends LatencyChart
{
    public int $height = 40;

    public bool $addStyle = false;

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

    protected function getIdentifier(): string
    {
        return Str::slug(get_class($this)).$this->monitorId;
    }
}
