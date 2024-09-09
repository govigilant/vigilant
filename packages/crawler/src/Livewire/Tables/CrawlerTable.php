<?php

namespace Vigilant\Crawler\Livewire\Tables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Enumerable;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Crawler\Enums\State;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Frontend\Integrations\Table\Enums\Status;
use Vigilant\Frontend\Integrations\Table\StatusColumn;

class CrawlerTable extends LivewireTable
{
    protected string $model = Crawler::class;

    protected array $pollingOptions = [
        '' => 'None',
        '10s' => 'Every 10 seconds',
    ];

    protected function columns(): array
    {
        return [
            Column::make(__('URL'), 'start_url')
                ->searchable()
                ->sortable(),

            Column::make(__('Status'), 'state')
                ->displayUsing(fn (State $state): string => __($state->label()))
                ->sortable(),

            StatusColumn::make(__('Issues'))
                ->text(function (Crawler $crawler) {
                    return __(':count issues', ['count' => $crawler->issueCount() ?? '0']);
                })
                ->status(function (Crawler $crawler): Status {
                    $count = $crawler->issueCount();

                    if ($count === null || $count === 0) {
                        return Status::Success;
                    }

                    $total = $crawler->totalUrlCount();

                    $threshold = $total * 0.05;

                    return $count > $threshold
                        ? Status::Danger
                        : Status::Warning;
                }),

            Column::make(__('URLs crawled'), function (Crawler $crawler): string {
                return sprintf(
                    '%d / %d',
                    $crawler->urls()->where('crawled', '=', true)->count(),
                    $crawler->urls()->count(),
                );
            }),
        ];
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Delete'), 'delete', function (Enumerable $models): void {
                $models->each(fn (Crawler $crawler) => $crawler->delete());
            }),
        ];
    }

    protected function link(Model $model): ?string
    {
        return route('crawler.view', ['crawler' => $model]);
    }
}
