<?php

namespace Vigilant\Uptime\Http\Livewire\Charts;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Vigilant\Frontend\Http\Livewire\BaseChart;
use Vigilant\Uptime\Models\ResultAggregate;

class LatencyChart extends BaseChart
{
    #[Locked]
    public int $monitorId = 0;

    public int $height = 200;

    public function mount(array $data): void
    {
        Validator::make($data, [
            'monitorId' => 'required',
        ])->validate();

        $this->monitorId = $data['monitorId'];
    }

    protected function points(): Collection
    {
        return ResultAggregate::query()
            ->where('monitor_id', '=', $this->monitorId)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();
    }

    public function data(): array
    {
        $points = $this->points();

        return [
            'type' => 'line',
            'data' => [
                'labels' => $points->pluck('created_at')->map(fn(Carbon $carbon): string => $carbon->format("d/m H:i")),
                'datasets' => [
                    [
                        'label' => 'Latency',
                        'data' => $points->pluck('total_time'),
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
                        'display' => true,
                    ],
                    'tooltip' => [
                        'enabled' => true,
                    ],
                ],
                'scales' => [
                    'y' => [
                        'display' => true,
                    ],
                    'x' => [
                        'display' => true,
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
