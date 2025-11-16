<?php

namespace Vigilant\Healthchecks\Http\Livewire\Charts;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Vigilant\Frontend\Http\Livewire\BaseChart;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Models\Metric;

class MetricChart extends BaseChart
{
    #[Locked]
    public int $healthcheckId = 0;

    public int $height = 200;

    public string $selectedKey = '';

    public array $availableKeys = [];

    public string $dateRange = 'week';

    public function mount(array $data): void
    {
        Validator::make($data, [
            'healthcheckId' => 'required',
        ])->validate();

        $this->healthcheckId = $data['healthcheckId'];

        $this->availableKeys = $this->getAvailableKeys()->toArray();
        
        if (!empty($this->availableKeys)) {
            $this->selectedKey = $this->availableKeys[0];
        }
    }

    public function setMetricKey(string $key): void
    {
        $this->selectedKey = $key;
        $this->loadChart();
    }

    public function setDateRange(string $range): void
    {
        $this->dateRange = $range;
        $this->loadChart();
    }

    protected function getDateRangeStart(): Carbon
    {
        return match ($this->dateRange) {
            'hour' => now()->subHour(),
            'day' => now()->subDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            '3months' => now()->subMonths(3),
            '6months' => now()->subMonths(6),
            default => now()->subWeek(),
        };
    }

    protected function getDateRangeOptions(): array
    {
        return [
            'hour' => 'Hour',
            'day' => 'Day',
            'week' => 'Week',
            'month' => 'Month',
            '3months' => '3 Months',
            '6months' => '6 Months',
        ];
    }

    protected function getAvailableKeys(): Collection
    {
        return Metric::query()
            ->where('healthcheck_id', '=', $this->healthcheckId)
            ->whereNotNull('key')
            ->where('key', '!=', '')
            ->selectRaw('`key`, COUNT(*) as count')
            ->groupBy('key')
            ->orderByDesc('count')
            ->get()
            ->pluck('key');
    }

    protected function points(): Collection
    {
        if (empty($this->selectedKey)) {
            return collect();
        }

        return Metric::query()
            ->where('healthcheck_id', '=', $this->healthcheckId)
            ->where('key', '=', $this->selectedKey)
            ->where('created_at', '>=', $this->getDateRangeStart())
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function data(): array
    {
        $points = $this->points();

        if ($points->isEmpty()) {
            return [
                'type' => 'line',
                'data' => [
                    'labels' => [],
                    'datasets' => [],
                ],
            ];
        }

        $labels = $points->pluck('created_at');
        $data = $points->pluck('value');
        $unit = $points->first()->unit ?? '';

        $dateFormat = match ($this->dateRange) {
            'hour' => 'H:i',
            'day' => 'd/m H:i',
            'week' => 'd/m H:i',
            'month' => 'd/m',
            '3months' => 'd/m',
            '6months' => 'd/m',
            default => 'd/m H:i',
        };

        $color = $this->getChartColor(0);

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels->map(fn (Carbon $carbon): string => teamTimezone($carbon)->format($dateFormat))->toArray(),
                'datasets' => [
                    [
                        'label' => $this->selectedKey,
                        'data' => $data->toArray(),
                        'pointRadius' => 1,
                        'pointHoverRadius' => 4,
                        'borderWidth' => 2,
                        'borderColor' => $color['border'],
                        'backgroundColor' => $color['bg'],
                        'fill' => true,
                        'tension' => 0.4,
                        'unit' => $unit,
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
                        'beginAtZero' => true,
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
        return Str::slug(get_class($this)).$this->healthcheckId;
    }

    public function render(): View
    {
        /** @var view-string $view */
        $view = 'healthchecks::livewire.charts.metric-chart';

        return view($view, [
            'identifier' => $this->getIdentifier(),
            'height' => $this->height,
            'availableKeys' => $this->getAvailableKeys(),
            'dateRangeOptions' => $this->getDateRangeOptions(),
        ]);
    }
}
