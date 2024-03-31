<?php

namespace Vigilant\Notifications\Http\Livewire\Tables;

use Illuminate\Database\Eloquent\Model;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Notifications\Models\Trigger;
use Vigilant\Notifications\Notifications\Notification;

class NotificationTable extends LivewireTable
{
    protected string $model = Trigger::class;

    protected function columns(): array
    {
        return [

            Column::make(__('Notification'), 'notification')
                ->displayUsing(function (string $notification) {
                    /** @var class-string<Notification> $notification */

                    return $notification::$name;
                }),

            Column::make(__('Channels'))
                ->displayUsing(function (Trigger $trigger) {
                    return $trigger->channels()->count() . ' Channel(s)';
                })

        ];
    }

    public function link(Model $model): ?string
    {
        return route('notifications.trigger.edit', ['trigger' => $model]);
    }
}
