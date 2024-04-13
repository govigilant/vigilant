<?php

namespace Vigilant\Notifications\Http\Livewire\Tables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Enumerable;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\BooleanColumn;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Filters\BooleanFilter;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Notifications\Facades\NotificationRegistry;
use Vigilant\Notifications\Models\Trigger;
use Vigilant\Notifications\Notifications\Notification;

class NotificationTable extends LivewireTable
{
    protected string $model = Trigger::class;

    protected function columns(): array
    {
        return [
            BooleanColumn::make(__('Enabled'), 'enabled')
                ->sortable(),

            Column::make(__('Name'), 'name')
                ->sortable()
                ->searchable(),

            Column::make(__('Type'), 'notification')
                ->displayUsing(function (string $notification) {
                    /** @var class-string<Notification> $notification */

                    return $notification::$name;
                }),

            Column::make(__('Channels'))
                ->displayUsing(function (Trigger $trigger) {
                    return $trigger->all_channels
                        ? __('All Channels')
                        : __(':count Channel(s)', ['count' => $trigger->channels()->count()]);
                }),
        ];
    }

    protected function filters(): array
    {
        return [
            BooleanFilter::make(__('Enabled'), 'enabled'),
            SelectFilter::make(__('Type'), 'notification')
                ->options(
                    collect(NotificationRegistry::notifications())
                        ->mapWithKeys(fn (string $notification): array => [$notification => $notification::$name])
                        ->toArray()
                )
        ];
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Enable'), 'enable', function (Enumerable $models): void {
                Trigger::query()
                    ->whereIn('id', $models->pluck('id'))
                    ->update([
                        'enabled' => true,
                    ]);
            }),

            Action::make(__('Disable'), 'disable', function (Enumerable $models): void {
                Trigger::query()
                    ->whereIn('id', $models->pluck('id'))
                    ->update([
                        'enabled' => false,
                    ]);
            }),

        ];
    }

    public function link(Model $model): ?string
    {
        return route('notifications.trigger.edit', ['trigger' => $model]);
    }
}
