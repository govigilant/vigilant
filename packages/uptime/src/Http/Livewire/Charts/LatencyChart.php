<?php

namespace Vigilant\Uptime\Http\Livewire\Charts;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Vigilant\Frontend\Http\Livewire\BaseChart;
use Vigilant\Uptime\Models\Result;
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
            ->get()
            ->sortBy('created_at');
    }

    public function data(): array
    {
        $points = $this->points();

        $labels = $points->pluck('created_at');
        $data = $points->pluck('total_time');

        $current = Result::query()
            ->where('monitor_id', '=', $this->monitorId)
            ->get();

        if ($data->isEmpty()) {
            $labels = $current->pluck('created_at');
            $data = $current->pluck('total_time');
        } else {
            if ($current->isNotEmpty()) {
                $currentTime = $current->max('created_at');
                $currentValue = $current->average('total_time');

                $labels->push($currentTime);
                $data->push($currentValue);
            }
        }

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels->map(fn (Carbon $carbon): string => teamTimezone($carbon)->format('d/m H:i'))->toArray(),
                'datasets' => [
                    [
                        'label' => 'Latency',
                        'data' => $data->toArray(),
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
