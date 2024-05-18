<?php

namespace Vigilant\Sites\Http\Livewire\Tables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Enumerable;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Sites\Models\Site;

class SiteTable extends LivewireTable
{
    protected string $model = Site::class;

    protected function columns(): array
    {
        return [
            Column::make(__('URL'), 'url'),
        ];
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Delete'), 'delete', function (Enumerable $models): void {
                $models->each(fn (Site $site) => $site->delete());
            }),
        ];
    }

    public function link(Model $model): ?string
    {
        return route('site.edit', ['site' => $model]);
    }
}
