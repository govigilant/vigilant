<?php

namespace Vigilant\Uptime\Http\Livewire\Charts;

use Livewire\Attributes\Locked;
use Vigilant\Frontend\Http\Livewire\BaseChart;
use Vigilant\Uptime\Models\ResultAggregate;

class LatencyChart extends BaseChart
{
    #[Locked]
    public int $monitorId = 0;

    public function mount(int $monitorId): void
    {
        $this->monitorId = $monitorId;
    }

    public function data(): array
    {

        $points = ResultAggregate::query()
            ->where('monitor_id', '=', $this->monitorId)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return [
            'type' => 'line',
            'data' => [
                'labels' => $points->pluck('total_time'),
                'datasets' => [
                    [
                        'label' => 'Latency',
                        'data' => $points->pluck('total_time'),
                    ],
                ],
            ],
            'options' => [
                'plugins' => [
                    'legend' => [
                        'display' => false,
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
}
