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
            $closestCountry = $this->getClosestCountry();

            if ($closestCountry && $countries->contains($closestCountry)) {
                $this->selectedCountries = [$closestCountry];
            } else {
                $this->selectedCountries = [$countries->first()];
            }
        }
    }

    public function toggleCountry(string $country): void
    {
        if (in_array($country, $this->selectedCountries)) {
            $this->selectedCountries = array_values(array_diff($this->selectedCountries, [$country]));
        } else {
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
        return ResultAggregate::query()
            ->where('monitor_id', '=', $this->monitorId)
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->selectRaw('country, COUNT(*) as count')
            ->groupBy('country')
            ->orderByDesc('count')
            ->get()
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
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function data(): array
    {
        if (count($this->selectedCountries) > 1) {
            return $this->multiCountryData();
        }

        return $this->singleLineData();
    }

    protected function singleLineData(): array
    {
        $points = $this->points();

        $labels = $points->pluck('created_at');
        $data = $points->pluck('total_time');

        $dateFormat = $this->dateRange === 'week' ? 'd/m H:i' : 'd/m';

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels->map(fn (Carbon $carbon): string => teamTimezone($carbon)->format($dateFormat))->toArray(),
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

        // Adjust limit based on date range to ensure we get sufficient data points
        $limit = match ($this->dateRange) {
            'week' => 168,      // ~1 week of hourly data
            'month' => 720,     // ~1 month of hourly data
            '3months' => 2160,  // ~3 months of hourly data
            '6months' => 4320,  // ~6 months of hourly data
            default => 168,
        };

        // First, collect all data points for each country
        $countryData = [];
        $allTimestamps = collect();

        foreach ($this->selectedCountries as $country) {
            $query = ResultAggregate::query()
                ->where('monitor_id', '=', $this->monitorId)
                ->where('country', '=', $country)
                ->where('created_at', '>=', $this->getDateRangeStart());

            $points = $query
                ->orderBy('created_at', 'asc')
                ->limit($limit)
                ->get();

            // Store data indexed by timestamp
            $countryData[$country] = [];
            foreach ($points as $point) {
                if ($point->created_at === null) {
                    continue;
                }
                $timestamp = $point->created_at->timestamp;
                $countryData[$country][$timestamp] = $point->total_time; // @phpstan-ignore-line
                $allTimestamps->push($timestamp);
            }
        }

        // Get unique timestamps sorted chronologically
        $uniqueTimestamps = $allTimestamps->unique()->sort()->values();

        // Build datasets with properly aligned data
        $datasets = [];
        foreach ($this->selectedCountries as $index => $country) {
            $data = [];

            // For each timestamp, use the value if it exists, otherwise null
            foreach ($uniqueTimestamps as $timestamp) {
                $data[] = $countryData[$country][$timestamp] ?? null;
            }

            $color = $colors[$index % count($colors)];

            $datasets[] = [
                'label' => strtoupper($country),
                'data' => $data,
                'pointRadius' => 0,
                'pointHoverRadius' => 0,
                'borderWidth' => 2,
                'borderColor' => $color,
                'tension' => 0.4,
                'unit' => 'ms',
                'spanGaps' => true,
            ];
        }

        $labels = $uniqueTimestamps->map(function ($timestamp) {
            return teamTimezone(Carbon::createFromTimestamp($timestamp))->format('d/m H:i');
        })->toArray();

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
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
