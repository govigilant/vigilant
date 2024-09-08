<?php

namespace Vigilant\Crawler\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Enumerable;
use Livewire\Attributes\Locked;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Crawler\Enums\Status;
use Vigilant\Crawler\Models\CrawledUrl;

class IssuesTable extends LivewireTable
{
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
            Column::make(__('URL'), 'url')
                ->searchable()
                ->sortable(),

            Column::make(__('Status'), 'status')
                ->displayUsing(fn (int $status): string => Status::tryFrom($status)?->label() ?? $status)
                ->searchable()
                ->sortable(),

            Column::make(__('Found On'), 'foundOn.url')
                ->searchable()
                ->sortable(),
        ];
    }

    protected function query(): Builder
    {
        return parent::query()
            ->where('web_crawled_urls.crawler_id', '=', $this->crawlerId)
            ->where('web_crawled_urls.status', '>=', 400);
    }

    /** @param  CrawledUrl  $model */
    protected function link(Model $model): ?string
    {
        return $model->url;
    }
}
