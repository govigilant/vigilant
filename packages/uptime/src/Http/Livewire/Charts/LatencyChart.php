<?php

namespace Vigilant\Uptime\Http\Livewire\Charts;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Vigilant\Frontend\Http\Livewire\BaseChart;
use Vigilant\Uptime\Models\Monitor;
use Vigilant\Uptime\Models\Result;
use Vigilant\Uptime\Models\ResultAggregate;

class LatencyChart extends BaseChart
{
    #[Locked]
    public int $monitorId = 0;

    public int $height = 200;

    public array $selectedCountries = [];

    public string $dateRange = 'week';

    public function mount(array $data): void
    {
        Validator::make($data, [
            'monitorId' => 'required',
        ])->validate();

        $this->monitorId = $data['monitorId'];

        $countries = $this->availableCountries();
        if ($countries->isNotEmpty()) {
            // Select the closest country by default if available
            $closestCountry = $this->getClosestCountry();

            if ($closestCountry && $countries->contains($closestCountry)) {
                $this->selectedCountries = [$closestCountry];
            } else {
                // Fallback to the most common country
                $this->selectedCountries = [$countries->first()];
            }
        }
    }

    public function toggleCountry(string $country): void
    {
        if (in_array($country, $this->selectedCountries)) {
            // Remove country from selection
            $this->selectedCountries = array_values(array_diff($this->selectedCountries, [$country]));
        } else {
            // Add country to selection
            $this->selectedCountries[] = $country;
        }

        $this->loadChart();
    }

    public function setDateRange(string $range): void
    {
        $this->dateRange = $range;
        $this->loadChart();
    }

    public function selectAllCountries(): void
    {
        $this->selectedCountries = $this->availableCountries()->toArray();
        $this->loadChart();
    }

    public function clearCountries(): void
    {
        $this->selectedCountries = [];
        $this->loadChart();
    }

    protected function getClosestCountry(): ?string
    {
        $monitor = Monitor::query()
            ->with('closestOutpost')
            ->find($this->monitorId);

        return $monitor?->closestOutpost?->country;
    }

    protected function getDateRangeStart(): Carbon
    {
        return match ($this->dateRange) {
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
            'week' => 'Week',
            'month' => 'Month',
            '3months' => '3 Months',
            '6months' => '6 Months',
        ];
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
            ->where('monitor_id', '=', $this->monitorId)
            ->where('created_at', '>=', $this->getDateRangeStart());

        if (! empty($this->selectedCountries)) {
            $query->whereIn('country', $this->selectedCountries);
        }

        return $query
            ->orderByDesc('created_at')
            ->take(100)
            ->get()
            ->sortBy('created_at');
    }

    public function data(): array
    {
        // If multiple countries selected, show separate lines for each country
        if (count($this->selectedCountries) > 1) {
            return $this->multiCountryData();
        }

        // Single country or all countries aggregated
        return $this->singleLineData();
    }

    protected function singleLineData(): array
    {
        $points = $this->points();

        $labels = $points->pluck('created_at');
        $data = $points->pluck('total_time');

        $currentQuery = Result::query()
            ->where('monitor_id', '=', $this->monitorId)
            ->where('created_at', '>=', $this->getDateRangeStart());

        if (! empty($this->selectedCountries)) {
            $currentQuery->whereIn('country', $this->selectedCountries);
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
                        'beginAtZero' => true,
                    ],
                    'x' => [
                        'display' => true,
                    ],
                ],
            ],
        ];
    }

    protected function multiCountryData(): array
    {
        $colors = [
            '#337F1F', // Green
            '#3B82F6', // Blue
            '#F59E0B', // Amber
            '#EF4444', // Red
            '#8B5CF6', // Purple
            '#EC4899', // Pink
            '#10B981', // Emerald
            '#F97316', // Orange
        ];

        $datasets = [];
        $allLabels = collect();

        foreach ($this->selectedCountries as $index => $country) {
            $query = ResultAggregate::query()
                ->where('monitor_id', '=', $this->monitorId)
                ->where('country', '=', $country)
                ->where('created_at', '>=', $this->getDateRangeStart());

            $points = $query
                ->orderByDesc('created_at')
                ->take(100)
                ->get()
                ->sortBy('created_at');

            $labels = $points->pluck('created_at');
            $data = $points->pluck('total_time');

            $currentQuery = Result::query()
                ->where('monitor_id', '=', $this->monitorId)
                ->where('country', '=', $country)
                ->where('created_at', '>=', $this->getDateRangeStart());

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

            $allLabels = $allLabels->merge($labels);

            $color = $colors[$index % count($colors)];

            $datasets[] = [
                'label' => strtoupper($country),
                'data' => $data->toArray(),
                'pointRadius' => 0,
                'pointHoverRadius' => 0,
                'borderWidth' => 2,
                'borderColor' => $color,
                'tension' => 0.4,
                'unit' => 'ms',
            ];
        }

        $uniqueLabels = $allLabels->unique()->sortBy(fn ($date) => $date)->values();

        return [
            'type' => 'line',
            'data' => [
                'labels' => $uniqueLabels->map(fn (Carbon $carbon): string => teamTimezone($carbon)->format('d/m H:i'))->toArray(),
                'datasets' => $datasets,
            ],
            'options' => [
                'plugins' => [
                    'legend' => [
                        'display' => true,
                    ],
                    'tooltip' => [
                        'enabled' => true,
                        'mode' => 'index',
                        'intersect' => false,
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
            'closestCountry' => $this->getClosestCountry(),
            'dateRangeOptions' => $this->getDateRangeOptions(),
        ]);
    }
}
