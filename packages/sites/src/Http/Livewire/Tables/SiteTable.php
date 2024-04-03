<?php

namespace Vigilant\Sites\Http\Livewire\Tables;

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
}
