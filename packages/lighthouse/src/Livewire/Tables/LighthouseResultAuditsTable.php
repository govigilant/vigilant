<?php

namespace Vigilant\Lighthouse\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
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
                ->displayUsing(fn(string $audit): string => Str::inlineMarkdown($audit))
                ->asHtml()
                ->sortable(),

            Column::make(__('Score'), 'score')

        ];
    }

    protected function query(): Builder
    {
        return parent::query()
            ->where('lighthouse_result_id', '=', $this->resultId);
    }

}
