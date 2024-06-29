<?php

namespace Vigilant\Lighthouse\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Frontend\Integrations\Table\HoverColumn;
use Vigilant\Lighthouse\Models\LighthouseResultAudit;

class LighthouseResultAuditsTable extends LivewireTable
{
    protected string $model = LighthouseResultAudit::class;

    protected bool $useSelection = false;

    #[Locked]
    public int $resultId = 0;

    public function mount(int $resultId)
    {
        $this->resultId = $resultId;
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Audit'), 'title')
                ->displayUsing(fn (string $audit): string => Str::inlineMarkdown($audit))
                ->asHtml()
                ->sortable(),

            HoverColumn::make(__('Description'), 'description')
                ->displayUsing(fn (mixed $value) => Str::markdown($value))
                ->asHtml(),

            HoverColumn::make(__('Explanation'), 'explanation')
                ->sortable()
                ->displayUsing(fn (mixed $value) => Str::markdown($value ?? ''))
                ->asHtml(),

            Column::make(__('Score'), 'score')
                ->displayUsing(fn (?float $score) => $score !== null ? ($score * 100).'%' : '-')
                ->sortable(),

            Column::make(__('Value'), 'numericValue')
                ->displayUsing(fn (?float $value) => $value !== null ? round($value, 2) : '-')
                ->sortable(),

            Column::make(__('Unit'), 'numericUnit')
                ->displayUsing(fn (?string $value) => $value !== null ? $value : '-')
                ->sortable(),
        ];
    }

    protected function query(): Builder
    {
        return parent::query()
            ->where('lighthouse_result_id', '=', $this->resultId);
    }

}
