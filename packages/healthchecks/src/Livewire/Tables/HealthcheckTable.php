<?php

namespace Vigilant\Healthchecks\Livewire\Tables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Gate;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use Vigilant\Frontend\Integrations\Table\BaseTable;
use Vigilant\Frontend\Integrations\Table\Enums\Status;
use Vigilant\Frontend\Integrations\Table\StatusColumn;
use Vigilant\Healthchecks\Enums\Status as HealthStatus;
use Vigilant\Healthchecks\Models\Healthcheck;
use Vigilant\Sites\Models\Site;

class HealthcheckTable extends BaseTable
{
    protected string $model = Healthcheck::class;

    protected array $pollingOptions = [
        '' => 'None',
        '30s' => 'Every 30 seconds',
    ];

    protected function columns(): array
    {
        return [
            StatusColumn::make(__('Status'))
                ->text(function (Healthcheck $healthcheck): string {
                    if (! $healthcheck->enabled) {
                        return __('Disabled');
                    }

                    if ($healthcheck->status === null) {
                        return __('Unknown');
                    }

                    return match ($healthcheck->status) {
                        HealthStatus::Healthy => __('Healthy'),
                        HealthStatus::Unhealthy => __('Unhealthy'),
                        default => __('Unknown'),
                    };
                })
                ->status(function (Healthcheck $healthcheck): Status {
                    if (! $healthcheck->enabled) {
                        return Status::Danger;
                    }

                    if ($healthcheck->status === null) {
                        return Status::Warning;
                    }

                    return match ($healthcheck->status) {
                        HealthStatus::Healthy => Status::Success,
                        HealthStatus::Unhealthy => Status::Danger,
                        default => Status::Warning,
                    };
                }),

            Column::make(__('Domain'), 'domain')
                ->searchable()
                ->sortable(),

            Column::make(__('Last check'), 'last_check_at')
                ->sortable(),
        ];
    }

    protected function filters(): array
    {
        return [
            SelectFilter::make(__('Site'), 'site_id')
                ->options(
                    Site::query()
                        ->orderBy('url')
                        ->pluck('url', 'id')
                        ->toArray()
                ),
        ];
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Enable'), function (Enumerable $models): void {
                foreach ($models as $model) {
                    if (! Gate::allows('create', $model)) {
                        break;
                    }

                    $model->update(['enabled' => true]);
                }
            }, 'enable'),

            Action::make(__('Disable'), function (Enumerable $models): void {
                $models->each(fn (Healthcheck $healthcheck) => $healthcheck->update(['enabled' => false]));
            }, 'disable'),

            Action::make(__('Delete'), function (Enumerable $models): void {
                $models->each(fn (Healthcheck $healthcheck) => $healthcheck->delete());
            }, 'delete'),
        ];
    }

    public function link(Model $model): ?string
    {
        return route('healthchecks.view', ['healthcheck' => $model]);
    }
}
