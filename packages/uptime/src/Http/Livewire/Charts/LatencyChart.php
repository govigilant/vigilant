<?php

namespace Vigilant\Uptime\Http\Livewire\Charts;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Vigilant\Frontend\Http\Livewire\BaseChart;
use Vigilant\Uptime\Models\Result;
use Vigilant\Uptime\Models\ResultAggregate;

class LatencyChart extends BaseChart
{
    #[Locked]
    public int $monitorId = 0;

    public int $height = 200;

    public ?string $selectedCountry = null;

    public function mount(array $data): void
    {
        Validator::make($data, [
            'monitorId' => 'required',
        ])->validate();

        $this->monitorId = $data['monitorId'];

        $countries = $this->availableCountries();
        if ($countries->isNotEmpty()) {
            $this->selectedCountry = $countries->first();
        }
    }

    public function selectCountry(?string $country): void
    {
        $this->selectedCountry = $country;
        $this->loadChart();
    }

    protected function availableCountries(): Collection
    {
        return Result::query()
            ->where('monitor_id', '=', $this->monitorId)
            ->whereNotNull('country')
            ->selectRaw('country, COUNT(*) as count')
            ->groupBy('country')
            ->orderByDesc('count')
            ->pluck('country');
    }

    protected function points(): Collection
    {
        $query = ResultAggregate::query()
            ->where('monitor_id', '=', $this->monitorId);

        if ($this->selectedCountry) {
            $query->where('country', '=', $this->selectedCountry);
        }

        return $query
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

        $currentQuery = Result::query()
            ->where('monitor_id', '=', $this->monitorId);

        if ($this->selectedCountry) {
            $currentQuery->where('country', '=', $this->selectedCountry);
        }

        $current = $currentQuery->get();

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
                        'unit' => 'ms',
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

    public function render(): View
    {
        /** @var view-string $view */
        $view = 'uptime::livewire.charts.latency-chart';

        return view($view, [
            'identifier' => $this->getIdentifier(),
            'height' => $this->height,
            'availableCountries' => $this->availableCountries(),
        ]);
    }
}
