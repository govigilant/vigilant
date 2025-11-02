<?php

namespace Vigilant\Frontend\Http\Livewire;

use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

abstract class BaseChart extends Component
{
    public int $height = 200;

    public bool $addStyle = true;

    abstract public function data(): array;

    public function loadChart(): void
    {
        $data = array_replace_recursive($this->defaultOptions(), $this->data());

        $this->dispatch($this->getIdentifier().'-update-chart', $data);
    }

    public function placeholder(): mixed
    {
        /** @var view-string $view */
        $view = 'frontend::livewire.charts.base-chart-placeholder';

        return view($view, [
            'height' => $this->height,
        ]);
    }

    public function render(): View
    {
        /** @var view-string $view */
        $view = 'frontend::livewire.charts.base-chart';

        return view($view, [
            'identifier' => $this->getIdentifier(),
            'height' => $this->height,
        ]);
    }

    public function defaultOptions(): array
    {
        return [
            'options' => [
                'maintainAspectRatio' => false,
                'responsive' => true,
                'plugins' => [
                    'legend' => [
                        'position' => 'top',
                        'align' => 'end',
                        'labels' => [
                            'color' => $this->getColor('text-secondary'),
                            'font' => [
                                'size' => 12,
                            ],
                            'padding' => 12,
                            'usePointStyle' => true,
                            'pointStyle' => 'circle',
                        ],
                    ],
                    'title' => [
                        'display' => false,
                    ],
                    'tooltip' => [
                        'position' => 'average',
                        'mode' => 'index',
                        'intersect' => false,
                        'backgroundColor' => $this->getColor('bg-elevated'),
                        'titleColor' => $this->getColor('text-primary'),
                        'bodyColor' => $this->getColor('text-secondary'),
                        'borderColor' => $this->getColor('border'),
                        'borderWidth' => 1,
                        'padding' => 12,
                    ],
                ],
                'scales' => [
                    'x' => [
                        'title' => [
                            'color' => $this->getColor('text-muted'),
                        ],
                        'grid' => [
                            'display' => false,
                        ],
                        'ticks' => [
                            'color' => $this->getColor('text-muted'),
                            'font' => [
                                'size' => 11,
                            ],
                            'maxRotation' => 0,
                        ],
                    ],
                    'y' => [
                        'title' => [
                            'color' => $this->getColor('text-muted'),
                        ],
                        'grid' => [
                            'color' => $this->getColor('grid'),
                            'drawBorder' => false,
                        ],
                        'ticks' => [
                            'color' => $this->getColor('text-muted'),
                            'font' => [
                                'size' => 11,
                            ],
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

    protected function dataset(array $dataset): array
    {
        return array_merge([
            'pointRadius' => 1,
            'pointHoverRadius' => 4,
            'borderCapStyle' => 'round',
            'borderJoinStyle' => 'round',
            'borderWidth' => 2,
            'tension' => 0.4,
        ], $dataset);
    }

    /**
     * Get a color from the design system
     * 
     * @param string $key Color key
     * @return string Hex color or rgba string
     */
    protected function getColor(string $key): string
    {
        return match ($key) {
            // Text colors
            'text-primary' => '#F4F4FA',     // base-100
            'text-secondary' => '#D8D8E8',   // base-200
            'text-muted' => '#A8A8C0',       // base-400
            
            // Background colors
            'bg-elevated' => '#232333',      // base-850
            'bg-main' => '#1A1A24',          // base-900
            
            // Border colors
            'border' => '#444459',           // base-700
            'grid' => '#2D2D42',             // base-800
            
            default => '#F4F4FA',
        };
    }

    /**
     * Get chart line colors from the design system
     * Returns an array of color sets with border and background
     * 
     * @return array<int, array{border: string, bg: string}>
     */
    protected function getChartColors(): array
    {
        return [
            ['border' => '#3B82F6', 'bg' => 'rgba(59, 130, 246, 0.1)'],   // blue
            ['border' => '#6366F1', 'bg' => 'rgba(99, 102, 241, 0.1)'],   // indigo
            ['border' => '#10B981', 'bg' => 'rgba(16, 185, 129, 0.1)'],   // green
            ['border' => '#F97316', 'bg' => 'rgba(249, 115, 22, 0.1)'],   // orange
            ['border' => '#8B5CF6', 'bg' => 'rgba(139, 92, 246, 0.1)'],   // purple
            ['border' => '#EC4899', 'bg' => 'rgba(236, 72, 153, 0.1)'],   // magenta
            ['border' => '#06B6D4', 'bg' => 'rgba(6, 182, 212, 0.1)'],    // cyan
            ['border' => '#EF4444', 'bg' => 'rgba(239, 68, 68, 0.1)'],    // red
        ];
    }

    /**
     * Get a specific chart color by index
     * 
     * @param int $index Color index (cycles through available colors)
     * @return array{border: string, bg: string}
     */
    protected function getChartColor(int $index): array
    {
        $colors = $this->getChartColors();
        return $colors[$index % count($colors)];
    }

    protected function getIdentifier(): string
    {
        return Str::slug(get_class($this));
    }
}
