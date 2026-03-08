<?php

namespace Vigilant\Crawler\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use Vigilant\Crawler\Models\CrawledUrl;
use Vigilant\Frontend\Integrations\Table\BaseTable;
use Vigilant\Frontend\Integrations\Table\LinkColumn;

class CrawledUrlsTable extends BaseTable
{
    protected string $model = CrawledUrl::class;

    public array $filters = [
        'crawled' => '',
    ];

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

            Column::make(__('Crawled'), 'crawled')
                ->displayUsing(fn (bool $crawled): string => $crawled ? __('Yes') : __('No'))
                ->sortable(),
        ];
    }

    protected function filters(): array
    {
        return [
            SelectFilter::make(__('Crawled'), 'crawled')
                ->options([
                    'yes' => __('Crawled'),
                    'no' => __('Not crawled'),
                ])
                ->filterUsing(function (Builder $builder, ?string $value): void {
                    if ($value === 'yes') {
                        $builder->where($builder->qualifyColumn('crawled'), '=', true);
                    } elseif ($value === 'no') {
                        $builder->where($builder->qualifyColumn('crawled'), '=', false);
                    }
                }),
        ];
    }

    protected function query(): Builder
    {
        return parent::query()
            ->where('web_crawled_urls.crawler_id', '=', $this->crawlerId);
    }
}
