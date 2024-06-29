<?php

namespace Vigilant\Frontend\Http\Livewire;

use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

abstract class BaseChart extends Component
{
    public int $height = 200;

    abstract public function data(): array;

    public function loadChart(): void
    {
        $data = cache()->remember(
            $this->getCacheKey(),
            now()->addHour(),
            fn () => array_merge_recursive($this->defaultOptions(), $this->data())
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
                'scaleShowValues' => true,
                'borderWidth' => 4,
                'borderJoinStyle' => 'round',
                'tension' => 0.4,
                'pointRadius' => .5,
                'pointHitRadius' => 10,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                    ],
                    'title' => [
                        'display' => false,
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

    protected function getIdentifier(): string
    {
        return Str::slug(get_class($this));
    }

    protected function getCacheKey(): string
    {
        return $this->getIdentifier();
    }
}
