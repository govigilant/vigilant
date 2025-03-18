<?php

namespace Vigilant\Lighthouse\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Enumerable;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Enums\Direction;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Frontend\Integrations\Table\Enums\Status;
use Vigilant\Frontend\Integrations\Table\StatusColumn;
use Vigilant\Lighthouse\Models\LighthouseMonitor;

class LighthouseMonitorsTable extends LivewireTable
{
    protected string $model = LighthouseMonitor::class;

    protected function columns(): array
    {
        return [
            StatusColumn::make(__('Status'))
                ->text(function (LighthouseMonitor $monitor): string {
                    return $monitor->enabled ? __('Enabled') : __('Disabled');
                })
                ->status(function (LighthouseMonitor $monitor): Status {
                    return $monitor->enabled ? Status::Success : Status::Danger;
                }),

            Column::make(__('URL'), 'url')
                ->sortable()
                ->searchable(),

            Column::make(__('Performance'), 'performance')
                ->displayUsing(fn (?float $value): string => static::scoreDisplay($value))
                ->asHtml()
                ->sortable(function (Builder $builder, Direction $direction): void {
                    if ($direction === Direction::Ascending) {
                        $builder->orderBy('lighthouse_results.performance');
                    } else {
                        $builder->orderByDesc('lighthouse_results.performance');
                    }
                }),

            Column::make(__('Accessibility'), 'accessibility')
                ->displayUsing(fn (?float $value): string => static::scoreDisplay($value))
                ->asHtml()
                ->sortable(function (Builder $builder, Direction $direction): void {
                    if ($direction === Direction::Ascending) {
                        $builder->orderBy('lighthouse_results.accessibility');
                    } else {
                        $builder->orderByDesc('lighthouse_results.accessibility');
                    }
                }),

            Column::make(__('Best Practices'), 'best_practices')
                ->displayUsing(fn (?float $value): string => static::scoreDisplay($value))
                ->asHtml()
                ->sortable(function (Builder $builder, Direction $direction): void {
                    if ($direction === Direction::Ascending) {
                        $builder->orderBy('lighthouse_results.best_practices');
                    } else {
                        $builder->orderByDesc('lighthouse_results.best_practices');
                    }
                }),

            Column::make(__('SEO'), 'seo')
                ->displayUsing(fn (?float $value): string => static::scoreDisplay($value))
                ->asHtml()
                ->sortable(function (Builder $builder, Direction $direction): void {
                    if ($direction === Direction::Ascending) {
                        $builder->orderBy('lighthouse_results.seo');
                    } else {
                        $builder->orderByDesc('lighthouse_results.seo');
                    }
                }),
        ];
    }

    public static function scoreDisplay(?float $value): string
    {
        if ($value === null) {
            return '-';
        }

        $percentage = round($value * 100);

        $color = match (true) {
            $percentage > 80 => 'text-green-light',
            $percentage >= 60 => 'text-orange-light',
            default => 'text-red-light'
        };

        return '<span class="'.$color.'">'.$percentage.'%</span>';
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Enable'), 'enable', function (Enumerable $models): void {
                foreach ($models as $model) {
                    if (! $this->authorize('create', $model)) {
                        break;
                    }

                    $model->update(['enabled' => true]);
                }
            }),

            Action::make(__('Disable'), 'disable', function (Enumerable $models): void {
                $models->each(fn (LighthouseMonitor $monitor) => $monitor->update(['enabled' => false]));
            }),

            Action::make(__('Delete'), 'delete', function (Enumerable $models): void {
                $models->each(fn (LighthouseMonitor $site): ?bool => $site->delete());
            }),
        ];
    }

    protected function appliedQuery(): Builder
    {
        return parent::appliedQuery()
            ->leftJoin('lighthouse_results', function (JoinClause $join): void {
                $join->on('lighthouse_monitors.id', '=', 'lighthouse_results.lighthouse_monitor_id')
                    ->whereRaw('lighthouse_results.id IN (SELECT MAX(id) FROM lighthouse_results GROUP BY lighthouse_monitor_id)');
            })->select([
                'lighthouse_monitors.*',
                'lighthouse_results.performance',
                'lighthouse_results.accessibility',
                'lighthouse_results.best_practices',
                'lighthouse_results.seo',
            ]);
    }

    protected function link(Model $model): ?string
    {
        return route('lighthouse.index', ['monitor' => $model]);
    }
}
