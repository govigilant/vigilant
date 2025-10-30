<?php

namespace Vigilant\Notifications\Http\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use Vigilant\Frontend\Integrations\Table\BaseTable;
use Vigilant\Frontend\Integrations\Table\DateColumn;
use Vigilant\Notifications\Channels\NotificationChannel;
use Vigilant\Notifications\Enums\Level;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Models\History;

class HistoryTable extends BaseTable
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
                ->displayUsing(function (?string $channel) {
                    if ($channel === null) {
                        return null;
                    }

                    /** @var class-string<NotificationChannel> $channel */
                    return $channel::$name;
                }),

            Column::make(__('Level'), 'data.level')
                ->displayUsing(fn (string $level) => Level::tryFrom($level)->name ?? $level),

            Column::make(__('Notification'), 'data.title')
                ->searchable(function (Builder $builder, mixed $search) {
                    $builder->where('data->title', 'LIKE', '%'.$search.'%');
                }),

            Column::make(__('Details'), 'data.description')
                ->searchable(function (Builder $builder, mixed $search) {
                    $builder->where('data->description', 'LIKE', '%'.$search.'%');
                }),

            DateColumn::make(__('Notified At'), 'created_at')
                ->sortable(),
        ];
    }

    protected function filters(): array
    {
        return [
            SelectFilter::make(__('Level'), 'data->level')
                ->options(
                    collect(Level::cases())
                        ->mapWithKeys(fn (Level $level) => [$level->value => $level->name])
                        ->toArray()
                ),

            SelectFilter::make(__('Channel'), 'channel_id')
                ->options(
                    Channel::query()
                        ->select(['id', 'channel'])
                        ->get()
                        ->mapwithKeys(function (Channel $channel): array {
                            /** @var class-string<NotificationChannel> $class */
                            $class = $channel->channel;

                            return [$channel->id => $class::$name];
                        })
                        ->toArray()
                ),
        ];
    }
}
