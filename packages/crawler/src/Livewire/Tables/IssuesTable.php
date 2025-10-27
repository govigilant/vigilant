<?php

namespace Vigilant\Crawler\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Enumerable;
use Livewire\Attributes\Locked;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Crawler\Enums\Status;
use Vigilant\Crawler\Jobs\CollectCrawlerStatsJob;
use Vigilant\Crawler\Models\CrawledUrl;
use Vigilant\Crawler\Models\Crawler;
use Vigilant\Crawler\Models\IgnoredUrl;
use Vigilant\Frontend\Integrations\Table\Actions\InlineAction;
use Vigilant\Frontend\Integrations\Table\ActionsColumn;
use Vigilant\Frontend\Integrations\Table\Concerns\HasInlineActions;
use Vigilant\Frontend\Integrations\Table\LinkColumn;

class IssuesTable extends LivewireTable
{
    use HasInlineActions;

    public array $filters = [
        'ignored_urls' => 'hide',
    ];

    protected string $model = CrawledUrl::class;

    #[Locked]
    public int $crawlerId;

    public function mount(int $crawlerId): void
    {
        $this->crawlerId = $crawlerId;
    }

    protected function columns(): array
    {
        return [
            LinkColumn::make(__('URL'), 'url')
                ->openInNewTab()
                ->searchable()
                ->sortable(),

            Column::make(__('Status'), 'status')
                ->displayUsing(fn (int $status): string => Status::tryFrom($status)?->label() ?? (string) $status)
                ->sortable(),

            LinkColumn::make(__('Found On'), 'foundOn.url')
                ->openInNewTab()
                ->searchable()
                ->sortable(),

            ActionsColumn::make(__('Actions'))
                ->actions([
                    InlineAction::make('ignoreUrl', __('Ignore'), 'phosphor-eye-slash-light')
                        ->visible(fn (CrawledUrl $url): bool => ! $url->ignored),

                    InlineAction::make('unignoreUrl', __('Unignore'), 'phosphor-eye-light')
                        ->visible(fn (CrawledUrl $url): bool => $url->ignored),
                ]),

        ];
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Ignore Selected'), function (Enumerable $models): void {
                foreach ($models as $model) {
                    IgnoredUrl::firstOrCreate([
                        'crawler_id' => $this->crawlerId,
                        'url_hash' => $model->url_hash,
                    ]);
                    $model->update(['ignored' => true]);
                }

                CollectCrawlerStatsJob::dispatch(Crawler::query()->findOrFail($this->crawlerId));
            }, 'ignoreUrl'),

            Action::make(__('Unignore Selected'), function (Enumerable $models): void {
                foreach ($models as $model) {
                    IgnoredUrl::query()
                        ->where('crawler_id', '=', $this->crawlerId)
                        ->where('url_hash', '=', $model->url_hash)
                        ->delete();

                    $model->ignored = false;
                    $model->save();
                }

                CollectCrawlerStatsJob::dispatch(Crawler::query()->findOrFail($this->crawlerId));
            }, 'unignoreUrl'),
        ];
    }

    protected function filters(): array
    {
        return [
            SelectFilter::make(__('Ignored URLs'), 'ignored_urls')
                ->options([
                    'hide' => __('Hide Ignored'),
                    'only' => __('Only Ignored'),
                ])
                ->filterUsing(function (Builder $builder, ?string $value): void {
                    if ($value === 'hide') {
                        $builder->where($builder->qualifyColumn('ignored'), '=', false);
                    } elseif ($value === 'only') {
                        $builder->where($builder->qualifyColumn('ignored'), '=', true);
                    }
                }),
        ];
    }

    protected function query(): Builder
    {
        return parent::query()
            ->where('web_crawled_urls.crawler_id', '=', $this->crawlerId)
            ->where(function (Builder $query): void {
                $query->where('web_crawled_urls.status', '>=', 400)
                    ->orWhere('web_crawled_urls.status', '=', 0);
            });
    }
}
