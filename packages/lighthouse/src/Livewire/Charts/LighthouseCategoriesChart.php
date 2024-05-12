<?php

namespace Vigilant\Lighthouse\Livewire\Charts;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Vigilant\Frontend\Http\Livewire\BaseChart;
use Vigilant\Lighthouse\Models\LighthouseResult;

class LighthouseCategoriesChart extends BaseChart
{
    #[Locked]
    public int $lighthouseSiteId = 0;

    public int $height = 200;

    public function mount(array $data): void
    {
        Validator::make($data, [
            'lighthouseSiteId' => 'required',
        ])->validate();

        $this->lighthouseSiteId = $data['lighthouseSiteId'];
    }

    public function data(): array
    {
        $results = LighthouseResult::query()
            ->where('lighthouse_site_id', '=', $this->lighthouseSiteId)
            ->where('created_at', '>', now()->subDays(90))
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
        return Str::slug(get_class($this)).$this->lighthouseSiteId;
    }
}
