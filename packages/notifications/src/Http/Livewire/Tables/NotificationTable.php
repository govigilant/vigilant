<?php

namespace Vigilant\Notifications\Http\Livewire\Tables;

use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Notifications\Models\Trigger;

class NotificationTable extends LivewireTable
{
    protected string $model = Trigger::class;

    protected function columns(): array
    {
        return [

            Column::make(__('Notification'), 'notification'),

        ];
    }
}
