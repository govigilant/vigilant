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
        $data = cache()->remember(
            $this->getCacheKey(),
            now()->addHour(),
            fn () => array_replace_recursive($this->defaultOptions(), $this->data())
        );

        $this->dispatch($this->getIdentifier().'-update-chart', $data);
    }

    public function placeholder(): mixed
    {
        return view('frontend::livewire.charts.base-chart-placeholder', [
            'height' => $this->height,
        ]);
    }

    public function render(): View
    {
        return view('frontend::livewire.charts.base-chart', [
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
                        'align' => 'center',
                    ],
                    'title' => [
                        'display' => false,
                    ],
                    'tooltip' => [
                        'position' => 'average',
                        'mode' => 'index',
                        'intersect' => false,
                    ],
                ],
                'scales' => [
                    'x' => [
                        'title' => [
                            'color' => '#ffffff',
                        ],
                        'grid' => [
                            'color' => '#403E3C',
                        ],
                    ],
                    'y' => [
                        'title' => [
                            'color' => '#ffffff',
                        ],
                        'grid' => [
                            'color' => '#403E3C',
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function dataset(array $dataset): array
    {
        return array_merge([
            'pointRadius' => 0,
            'pointHoverRadius' => 1,
            'borderCapStyle' => 'round',
            'borderJoinStyle' => 'round',
            'borderWidth' => 4,
            'tension' => 0.6,
        ], $dataset);
    }

    protected function getIdentifier(): string
    {
        return Str::slug(get_class($this));
    }

    protected function getCacheKey(): string
    {
        return $this->getIdentifier();
    }
}
