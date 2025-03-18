<?php

namespace Vigilant\Crawler\Livewire\Tables;

use Cron\CronExpression;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Enumerable;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Crawler\Actions\StartCrawler;
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
            StatusColumn::make(__('Status'))
                ->text(function (Crawler $crawler): string {
                    return $crawler->enabled ? __('Enabled') : __('Disabled');
                })
                ->status(function (Crawler $crawler): Status {
                    return $crawler->enabled ? Status::Success : Status::Danger;
                }),

            Column::make(__('URL'), 'start_url')
                ->searchable()
                ->sortable(),

            Column::make(__('Next Run'), 'schedule')
                ->displayUsing(function (string $schedule, Crawler $crawler): ?string {
                    if (! $crawler->enabled) {
                        return null;
                    }

                    $expression = new CronExpression($schedule);
                    $nextRun = Carbon::parse($expression->getNextRunDate());

                    return $nextRun->diffForHumans();
                })
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
            Action::make(__('Enable'), 'enable', function (Enumerable $models): void {
                foreach ($models as $model) {
                    if (! $this->authorize('create', $model)) {
                        break;
                    }

                    $model->update(['enabled' => true]);
                }
            }),

            Action::make(__('Disable'), 'disable', function (Enumerable $models): void {
                $models->each(fn (Crawler $crawler) => $crawler->update(['enabled' => false]));
            }),

            Action::make(__('Start Crawler'), 'start', function (Enumerable $models): void {
                /** @var StartCrawler $starter */
                $starter = app(StartCrawler::class);

                $models
                    ->where('state', '!=', State::Crawling)
                    ->each(fn (Crawler $crawler) => $starter->start($crawler));
            }),

            Action::make(__('Delete'), 'delete', function (Enumerable $models): void {
                $models->each(fn (Crawler $crawler): ?bool => $crawler->delete());
            }),
        ];
    }

    protected function link(Model $model): ?string
    {
        return route('crawler.view', ['crawler' => $model]);
    }
}
