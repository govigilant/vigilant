<?php

namespace Vigilant\Crawler\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Crawler\Enums\Status;
use Vigilant\Crawler\Models\CrawledUrl;
use Vigilant\Frontend\Integrations\Table\LinkColumn;

class IssuesTable extends LivewireTable
{
    protected string $model = CrawledUrl::class;

    #[Locked]
    public int $crawlerId;

    protected bool $useSelection = false;

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
                ->searchable()
                ->sortable(),

            LinkColumn::make(__('Found On'), 'foundOn.url')
                ->openInNewTab()
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
}
