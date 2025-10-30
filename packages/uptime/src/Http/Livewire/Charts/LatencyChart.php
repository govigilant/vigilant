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
            'hour' => now()->subHour(),
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
            'week' => 'Week',
            'month' => 'Month',
            '3months' => '3 Months',
            '6months' => '6 Months',
        ];
    }

    protected function availableCountries(): Collection
    {
        // For hour range, use Result table; for others, use ResultAggregate
        $model = $this->dateRange === 'hour' ? Result::class : ResultAggregate::class;

        return $model::query()
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
        // For hour range, use Result table; for others, use ResultAggregate
        $model = $this->dateRange === 'hour' ? Result::class : ResultAggregate::class;

        $query = $model::query()
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

        if ($this->dateRange === 'hour') {
            $dateFormat = 'H:i';
        }

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
        // Design system colors from styleguide
        $colors = [
            ['border' => '#3B82F6', 'bg' => 'rgba(59, 130, 246, 0.1)'],   // blue
            ['border' => '#6366F1', 'bg' => 'rgba(99, 102, 241, 0.1)'],   // indigo
            ['border' => '#10B981', 'bg' => 'rgba(16, 185, 129, 0.1)'],   // green
            ['border' => '#F97316', 'bg' => 'rgba(249, 115, 22, 0.1)'],   // orange
            ['border' => '#8B5CF6', 'bg' => 'rgba(139, 92, 246, 0.1)'],   // purple
            ['border' => '#EC4899', 'bg' => 'rgba(236, 72, 153, 0.1)'],   // magenta
            ['border' => '#06B6D4', 'bg' => 'rgba(6, 182, 212, 0.1)'],    // cyan
            ['border' => '#EF4444', 'bg' => 'rgba(239, 68, 68, 0.1)'],    // red
        ];

        $limit = match ($this->dateRange) {
            'hour' => 60,       // ~1 hour of minute data
            'week' => 168,      // ~1 week of hourly data
            'month' => 720,     // ~1 month of hourly data
            '3months' => 2160,  // ~3 months of hourly data
            '6months' => 4320,  // ~6 months of hourly data
            default => 168,
        };

        $model = $this->dateRange === 'hour' ? Result::class : ResultAggregate::class;

        $countryData = [];
        $allTimestamps = collect();

        foreach ($this->selectedCountries as $country) {
            $query = $model::query()
                ->where('monitor_id', '=', $this->monitorId)
                ->where('country', '=', $country)
                ->where('created_at', '>=', $this->getDateRangeStart());

            $points = $query
                ->orderBy('created_at', 'asc')
                ->limit($limit)
                ->get();

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

        $uniqueTimestamps = $allTimestamps->unique()->sort()->values();

        $datasets = [];
        foreach ($this->selectedCountries as $index => $country) {
            $data = [];

            foreach ($uniqueTimestamps as $timestamp) {
                $data[] = $countryData[$country][$timestamp] ?? null;
            }

            $colorSet = $colors[$index % count($colors)];

            $datasets[] = [
                'label' => strtoupper($country),
                'data' => $data,
                'pointRadius' => 1,
                'pointHoverRadius' => 4,
                'borderWidth' => 2,
                'borderColor' => $colorSet['border'],
                'backgroundColor' => $colorSet['bg'],
                'fill' => true,
                'tension' => 0.4,
                'unit' => 'ms',
                'spanGaps' => true,
            ];
        }

        $labels = $uniqueTimestamps->map(function ($timestamp) {
            $dateFormat = $this->dateRange === 'hour' ? 'H:i' : 'd/m H:i';

            return teamTimezone(Carbon::createFromTimestamp($timestamp))->format($dateFormat);
        })->toArray();

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => $datasets,
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'top',
                        'align' => 'end',
                        'labels' => [
                            'color' => '#D8D8E8', // base-200
                            'font' => [
                                'size' => 12,
                            ],
                            'padding' => 12,
                            'usePointStyle' => true,
                            'pointStyle' => 'circle',
                        ],
                    ],
                    'tooltip' => [
                        'enabled' => true,
                        'mode' => 'index',
                        'intersect' => false,
                        'backgroundColor' => '#232333', // base-850
                        'titleColor' => '#F4F4FA', // base-100
                        'bodyColor' => '#D8D8E8', // base-200
                        'borderColor' => '#444459', // base-700
                        'borderWidth' => 1,
                        'padding' => 12,
                    ],
                ],
                'scales' => [
                    'y' => [
                        'display' => true,
                        'beginAtZero' => true,
                        'grid' => [
                            'color' => '#2D2D42', // base-800
                            'drawBorder' => false,
                        ],
                        'ticks' => [
                            'color' => '#A8A8C0', // base-400
                            'font' => [
                                'size' => 11,
                            ],
                        ],
                    ],
                    'x' => [
                        'display' => true,
                        'grid' => [
                            'display' => false,
                        ],
                        'ticks' => [
                            'color' => '#A8A8C0', // base-400
                            'font' => [
                                'size' => 11,
                            ],
                            'maxRotation' => 0,
                        ],
                    ],
                ],
                'interaction' => [
                    'mode' => 'index',
                    'intersect' => false,
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
