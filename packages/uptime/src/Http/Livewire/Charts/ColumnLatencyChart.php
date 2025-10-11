<?php

namespace Vigilant\Uptime\Http\Livewire\Charts;

use Illuminate\View\View;

class ColumnLatencyChart extends LatencyChart
{
    public int $height = 40;

    public function mount(array $data): void
    {
        parent::mount($data);
        
        // Force date range to week for column chart
        $this->dateRange = 'week';
        
        // Ensure we have the closest country selected
        $closestCountry = $this->getClosestCountry();
        if ($closestCountry) {
            $countries = $this->availableCountries();
            if ($countries->contains($closestCountry)) {
                $this->selectedCountries = [$closestCountry];
            }
        }
    }

    public function data(): array
    {
        $points = $this->points()->pluck('total_time');

        return [
            'type' => 'line',
            'data' => [
                'labels' => $points,
                'datasets' => [
                    [
                        'label' => 'Latency',
                        'data' => $points,
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
                        'display' => false,
                    ],
                    'tooltip' => [
                        'enabled' => false,
                    ],
                ],
                'scales' => [
                    'y' => [
                        'display' => false,
                        'beginAtZero' => true,
                    ],
                    'x' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }

    public function render(): View
    {
        /** @var view-string $view */
        $view = 'uptime::livewire.charts.column-latency-chart';

        return view($view, [
            'identifier' => $this->getIdentifier(),
            'height' => $this->height,
            'hasPoints' => $this->points()->isNotEmpty(),
        ]);
    }
}
