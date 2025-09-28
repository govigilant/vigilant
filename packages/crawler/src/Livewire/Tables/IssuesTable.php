<?php

namespace Vigilant\Crawler\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Enumerable;
use Livewire\Attributes\Locked;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Crawler\Enums\Status;
use Vigilant\Crawler\Models\CrawledUrl;
use Vigilant\Crawler\Models\IgnoredUrl;
use Vigilant\Frontend\Integrations\Table\Actions\InlineAction;
use Vigilant\Frontend\Integrations\Table\ActionsColumn;
use Vigilant\Frontend\Integrations\Table\Concerns\HasInlineActions;
use Vigilant\Frontend\Integrations\Table\LinkColumn;

class IssuesTable extends LivewireTable
{
    use HasInlineActions;

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
                    InlineAction::make('ignoreUrl', __('Ignore'), 'phosphor-eye-slash-light'),
                ]),

        ];
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Ignore Selected'), 'ignoreUrl', function (Enumerable $models): void {
                foreach ($models as $model) {
                    IgnoredUrl::firstOrCreate([
                        'crawler_id' => $this->crawlerId,
                        'url_hash' => md5($model->url),
                    ]);
                    $model->delete();
                }
            }),
        ];
    }

    protected function query(): Builder
    {
        return parent::query()
            ->where('web_crawled_urls.crawler_id', '=', $this->crawlerId)
            ->where('web_crawled_urls.status', '>=', 400);
    }
}
