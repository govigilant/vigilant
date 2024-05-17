<?php

namespace Vigilant\Lighthouse\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Lighthouse\Models\LighthouseResult;

class LighthouseResultsTable extends LivewireTable
{
    protected string $model = LighthouseResult::class;

    protected bool $useSelection = false;

    public string $sortColumn = 'created_at';

    public string $sortDirection = 'desc';

    #[Locked]
    public int $siteId = 0;

    public function mount(int $siteId)
    {
        $this->siteId = $siteId;
    }

    protected function columns(): array
    {
        return [
            DateColumn::make(__('Ran At'), 'created_at')
                ->sortable(),

            Column::make(__('Performance'), 'performance')
                ->displayUsing(fn (?float $value): string => $this->scoreDisplay($value))
                ->asHtml()
                ->sortable(),

            Column::make(__('Accessibility'), 'accessibility')
                ->displayUsing(fn (?float $value): string => $this->scoreDisplay($value))
                ->asHtml()
                ->sortable(),

            Column::make(__('Best Practices'), 'best_practices')
                ->displayUsing(fn (?float $value): string => $this->scoreDisplay($value))
                ->asHtml()
                ->sortable(),

            Column::make(__('SEO'), 'seo')
                ->displayUsing(fn (?float $value): string => $this->scoreDisplay($value))
                ->asHtml()
                ->sortable(),
        ];
    }

    protected function scoreDisplay(?float $value): string
    {
        if ($value === null) {
            return '-';
        }

        $percentage = round($value * 100);

        $color = match (true) {
            $percentage > 60 => 'text-orange-light',
            $percentage > 80 => 'text-green-light',
            default => 'text-red-light'
        };

        return '<span class="'.$color.'">'.$percentage.'%</span>';
    }

    protected function query(): Builder
    {
        return parent::query()
            ->where('lighthouse_site_id', '=', $this->siteId);
    }

}
