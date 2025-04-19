<?php

namespace Vigilant\Cve\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as Query;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Enums\Direction;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Cve\Actions\ImportAllCves;
use Vigilant\Cve\Models\CveMonitor;
use Vigilant\Frontend\Integrations\Table\Enums\Status;
use Vigilant\Frontend\Integrations\Table\StatusColumn;

class CveMonitorTable extends LivewireTable
{
    protected string $model = CveMonitor::class;

    protected function columns(): array
    {
        return [
            StatusColumn::make(__('Status'))
                ->text(function (CveMonitor $monitor): string {
                    return $monitor->enabled ? __('Enabled') : __('Disabled');
                })
                ->status(function (CveMonitor $monitor): Status {
                    return $monitor->enabled ? Status::Success : Status::Danger;
                }),

            Column::make(__('Keyword'), 'keyword')
                ->searchable()
                ->sortable(),

            Column::make(__('Matched CVE\'s'), 'total_matches')
                ->sortable(function (Builder $builder, Direction $direction): void {
                    $builder->orderBy(function (Query $query): void {
                        $query->selectRaw('COUNT(*)')
                            ->from('cve_monitor_matches')
                            ->whereColumn('cve_monitor_matches.cve_monitor_id', 'cve_monitors.id');
                    }, $direction->value);
                })->searchable(function (Builder $builder, mixed $value): void {
                    $builder->where(function (Query $query): void {
                        $query->selectRaw('COUNT(*)')
                            ->from('cve_monitor_matches')
                            ->whereColumn('cve_monitor_matches.cve_monitor_id', 'cve_monitors.id');
                    }, '=', $value);
                }),
        ];
    }

    protected function link(Model $record): string
    {
        return route('cve.monitor.view', ['monitor' => $record]);
    }

    protected function actions(): array
    {
        $actions = [
            Action::make(__('Enable'), 'enable', function (Enumerable $models): void {
                foreach ($models as $model) {
                    if (! Gate::allows('create', $model)) {
                        break;
                    }

                    $model->update(['enabled' => true]);
                }
            }),

            Action::make(__('Disable'), 'disable', function (Enumerable $models): void {
                $models->each(fn (CveMonitor $monitor) => $monitor->update(['enabled' => false]));
            }),

            Action::make(__('Delete'), 'delete', function (Enumerable $models): void {
                $models->each(fn (CveMonitor $monitor) => $monitor->delete());
            }),
        ];

        if (ce()) {
            $actions[] = Action::make(__('Import all CVE\'s'), 'import', function (): void {
                $importer = app(ImportAllCves::class);
                $importer->import();
            })->standalone();
        }

        return $actions;
    }

    protected function applySelect(Builder $builder): static
    {
        parent::applySelect($builder);

        $builder->addSelect(
            DB::raw('(
    SELECT COUNT(`cve_monitor_matches`.`id`)
    FROM `cve_monitor_matches`
    WHERE `cve_monitor_matches`.`cve_monitor_id` = `cve_monitors`.`id`
) AS total_matches')
        );

        return $this;
    }
}
