<?php

namespace Vigilant\Lighthouse\Livewire\Tables;

use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Lighthouse\Models\LighthouseSite;

class LighthouseSitesTable extends LivewireTable
{
    protected string $model = LighthouseSite::class;

    protected function columns(): array
    {
        return [
            Column::make(__('URL'), 'url'),
        ];
    }
}
