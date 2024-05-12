<?php

namespace Vigilant\Lighthouse\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Enums\Direction;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Lighthouse\Models\LighthouseSite;

class LighthouseSitesTable extends LivewireTable
{
    protected string $model = LighthouseSite::class;

    protected bool $useSelection = false;

    protected function columns(): array
    {
        return [
            Column::make(__('URL'), 'url'),

            Column::make(__('Performance'), 'performance')
                ->displayUsing(fn (?float $value): string => $this->scoreDisplay($value))
                ->asHtml()
                ->sortable(function (Builder $builder, Direction $direction): void {
                    if ($direction === Direction::Ascending) {
                        $builder->orderBy('lighthouse_results.performance');
                    } else {
                        $builder->orderByDesc('lighthouse_results.performance');
                    }
                }),

            Column::make(__('Accessibility'), 'accessibility')
                ->displayUsing(fn (?float $value): string => $this->scoreDisplay($value))
                ->asHtml()
                ->sortable(function (Builder $builder, Direction $direction): void {
                    if ($direction === Direction::Ascending) {
                        $builder->orderBy('lighthouse_results.accessibility');
                    } else {
                        $builder->orderByDesc('lighthouse_results.accessibility');
                    }
                }),

            Column::make(__('Best Practices'), 'best_practices')
                ->displayUsing(fn (?float $value): string => $this->scoreDisplay($value))
                ->asHtml()
                ->sortable(function (Builder $builder, Direction $direction): void {
                    if ($direction === Direction::Ascending) {
                        $builder->orderBy('lighthouse_results.best_practices');
                    } else {
                        $builder->orderByDesc('lighthouse_results.best_practices');
                    }
                }),

            Column::make(__('SEO'), 'seo')
                ->displayUsing(fn (?float $value): string => $this->scoreDisplay($value))
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
            ->leftJoin('lighthouse_results', function (JoinClause $join): void {
                $join->on('lighthouse_sites.id', '=', 'lighthouse_results.lighthouse_site_id')
                    ->whereRaw('lighthouse_results.id IN (SELECT MAX(id) FROM lighthouse_results GROUP BY lighthouse_site_id)');
            })
            ->select([
                'lighthouse_results.performance',
                'lighthouse_results.accessibility',
                'lighthouse_results.best_practices',
                'lighthouse_results.seo',
            ]);
    }

    protected function link(Model $model): ?string
    {
        return route('lighthouse.index', ['lighthouseSite' => $model]);
    }
}
