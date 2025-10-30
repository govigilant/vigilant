<?php

namespace Vigilant\Lighthouse\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use RamonRietdijk\LivewireTables\Columns\Column;
use Vigilant\Frontend\Integrations\Table\BaseTable;
use Vigilant\Frontend\Integrations\Table\DateColumn;
use Vigilant\Lighthouse\Models\LighthouseResult;

class LighthouseResultsTable extends BaseTable
{
    protected string $model = LighthouseResult::class;

    public string $sortColumn = 'created_at';

    public string $sortDirection = 'desc';

    #[Locked]
    public int $monitorId = 0;

    public function mount(int $monitorId): void
    {
        $this->monitorId = $monitorId;
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

    protected function link(Model $model): ?string
    {
        return route('lighthouse.result.index', ['result' => $model]);
    }

    protected function query(): Builder
    {
        return parent::query()
            ->where('lighthouse_monitor_id', '=', $this->monitorId)
            ->whereNull('batch_id');
    }
}
