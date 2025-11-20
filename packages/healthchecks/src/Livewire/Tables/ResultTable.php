<?php

namespace Vigilant\Healthchecks\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;
use RamonRietdijk\LivewireTables\Columns\Column;
use Vigilant\Frontend\Integrations\Table\BaseTable;
use Vigilant\Frontend\Integrations\Table\DateColumn;
use Vigilant\Frontend\Integrations\Table\Enums\Status as TableStatus;
use Vigilant\Frontend\Integrations\Table\StatusColumn;
use Vigilant\Healthchecks\Enums\Status;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Healthchecks\Models\Result;

class ResultTable extends BaseTable
{
    protected string $model = Result::class;

    #[Locked]
    public int $healthcheckId = 0;

    public string $sortColumn = 'created_at';

    public string $sortDirection = 'desc';

    public function mount(int $healthcheckId): void
    {
        $this->healthcheckId = $healthcheckId;
        Healthcheck::query()->findOrFail($healthcheckId);
    }

    protected function columns(): array
    {
        return [
            DateColumn::make(__('Date'), 'created_at')
                ->sortable(),

            Column::make(__('Key'), 'key')
                ->sortable(),

            StatusColumn::make(__('Status'))
                ->text(function (Result $result): string {
                    return match ($result->status) {
                        Status::Healthy => __('Healthy'),
                        Status::Warning => __('Warning'),
                        Status::Unhealthy => __('Unhealthy'),
                    };
                })
                ->status(function (Result $result): TableStatus {
                    return match ($result->status) {
                        Status::Healthy => TableStatus::Success,
                        Status::Warning => TableStatus::Warning,
                        Status::Unhealthy => TableStatus::Danger,
                    };
                }),

            Column::make(__('Message'), 'message'),
        ];
    }

    protected function query(): Builder
    {
        return parent::query()
            ->where('healthcheck_id', '=', $this->healthcheckId);
    }
}
