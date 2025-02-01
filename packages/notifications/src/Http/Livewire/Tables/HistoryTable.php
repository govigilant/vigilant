<?php

namespace Vigilant\Notifications\Http\Livewire\Tables;

use Illuminate\Support\Enumerable;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Frontend\Integrations\Table\DateColumn;
use Vigilant\Notifications\Channels\NotificationChannel;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Models\History;

class HistoryTable extends LivewireTable
{
    protected string $model = History::class;

    public string $sortColumn = 'created_at';

    public string $sortDirection = 'desc';

    protected function columns(): array
    {
        return [
            Column::make(__('Type'), 'trigger.name')
                ->searchable(),

            Column::make(__('Channel'), 'channel.channel')
                ->searchable()
                ->displayUsing(function (string $channel) {
                    /** @var class-string<NotificationChannel> $channel */
                    return $channel::$name;
                }),

            Column::make(__('Level'), 'data.level')
                ->searchable()
                ->displayUsing(fn (string $level) => Level::tryFrom($level)?->name ?? $level),

            Column::make(__('Notification'), 'data.title')
                ->searchable(),

            Column::make(__('Details'), 'data.description')
                ->searchable(),

            DateColumn::make(__('Notified At'), 'created_at')
                ->sortable(),
        ];
    }

    protected function actions(): array
    {
        return [
            // Action::make(__('Delete'), 'delete', function (Enumerable $models): void {
            //    $models->each(fn (Channel $channel) => $channel->delete());
            // }),
        ];
    }
}
