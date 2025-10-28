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
            ->whereNull('batch_id')
            ->get();

        $labels = $results->pluck('created_at')->map(fn (Carbon $carbon): string => $carbon->toDateTimeString());

        $colors = $this->getChartColors();

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    $this->dataset([
                        'label' => 'Performance',
                        'data' => $results->pluck('performance')->map(fn (float $value): float => $value * 100),
                        'borderColor' => $colors[0]['border'], // blue
                        'backgroundColor' => $colors[0]['bg'],
                        'fill' => true,
                        'unit' => '%',
                    ]),
                    $this->dataset([
                        'label' => 'Accessibility',
                        'data' => $results->pluck('accessibility')->map(fn (float $value): float => $value * 100),
                        'borderColor' => $colors[2]['border'], // green
                        'backgroundColor' => $colors[2]['bg'],
                        'fill' => true,
                        'unit' => '%',
                    ]),
                    $this->dataset([
                        'label' => 'Best Practices',
                        'data' => $results->pluck('best_practices')->map(fn (float $value): float => $value * 100),
                        'borderColor' => $colors[4]['border'], // purple
                        'backgroundColor' => $colors[4]['bg'],
                        'fill' => true,
                        'unit' => '%',
                    ]),
                    $this->dataset([
                        'label' => 'SEO',
                        'data' => $results->pluck('seo')->map(fn (float $value): float => $value * 100),
                        'borderColor' => $colors[3]['border'], // orange
                        'backgroundColor' => $colors[3]['bg'],
                        'fill' => true,
                        'unit' => '%',
                    ]),
                ],
            ],
            'options' => [
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'top',
                        'align' => 'start',
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
