<?php

namespace Vigilant\Lighthouse\Livewire\Charts;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Vigilant\Frontend\Http\Livewire\BaseChart;
use Vigilant\Lighthouse\Models\LighthouseResult;

#[Lazy]
#[Isolate]
class LighthouseCategoriesChart extends BaseChart
{
    #[Locked]
    public int $lighthouseMonitorId = 0;

    public int $height = 200;

    public function mount(array $data): void
    {
        Validator::make($data, [
            'lighthouseMonitorId' => 'required',
        ])->validate();

        $this->lighthouseMonitorId = $data['lighthouseMonitorId'];
    }

    public function data(): array
    {
        $results = LighthouseResult::query()
            ->where('lighthouse_monitor_id', '=', $this->lighthouseMonitorId)
            ->get();

        $labels = $results->pluck('created_at')->map(fn (Carbon $carbon): string => $carbon->toDateTimeString());

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Performance',
                        'data' => $results->pluck('performance')->map(fn (float $value): float => $value * 100),
                        'pointRadius' => 0,
                        'pointHoverRadius' => 0,
                        'borderWidth' => 2,
                        'borderColor' => '#205EA6',
                        'tension' => 0.4,
                    ],
                    [
                        'label' => 'Accessibility',
                        'data' => $results->pluck('accessibility')->map(fn (float $value): float => $value * 100),
                        'pointRadius' => 0,
                        'pointHoverRadius' => 0,
                        'borderWidth' => 2,
                        'borderColor' => '#5E409D',
                        'tension' => 0.4,
                    ],
                    [
                        'label' => 'Best Practices',
                        'data' => $results->pluck('best_practices')->map(fn (float $value): float => $value * 100),
                        'pointRadius' => 0,
                        'pointHoverRadius' => 0,
                        'borderWidth' => 2,
                        'borderColor' => '#A02F6F',
                        'tension' => 0.4,
                    ],
                    [
                        'label' => 'SEO',
                        'data' => $results->pluck('seo')->map(fn (float $value): float => $value * 100),
                        'pointRadius' => 0,
                        'pointHoverRadius' => 0,
                        'borderWidth' => 2,
                        'borderColor' => '#24837B',
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
                        'min' => 0,
                        'max' => 100,
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
        return Str::slug(get_class($this)).$this->lighthouseMonitorId;
    }
}
