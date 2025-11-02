<?php

namespace Vigilant\Notifications\Http\Livewire\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\DB;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Enums\Direction;
use Vigilant\Frontend\Integrations\Table\BaseTable;
use Vigilant\Notifications\Channels\NotificationChannel;
use Vigilant\Notifications\Models\Channel;

class ChannelTable extends BaseTable
{
    protected string $model = Channel::class;

    protected function columns(): array
    {
        return [
            Column::make(__('Channel'), 'channel')
                ->displayUsing(function (string $channel) {
                    /** @var class-string<NotificationChannel> $channel */

                    return $channel::$name;
                }),

            Column::make(__('Notifications Sent'), 'total_notification_history')
                ->sortable(function (Builder $builder, Direction $direction): void {
                    $builder->orderBy(function (QueryBuilder $query): void {
                        $query->selectRaw('COUNT(*)')->from('notification_history')->where('channel_id', '=', DB::raw('notification_channels.id'));
                    }, $direction->value);
                }),
        ];
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Delete'), function (Enumerable $models): void {
                $models->each(fn (Channel $channel) => $channel->delete());
            }, 'delete'),
        ];
    }

    public function link(Model $model): ?string
    {
        return route('notifications.channel.edit', ['channel' => $model]);
    }

    protected function applySelect(Builder $builder): static
    {
        parent::applySelect($builder);

        $builder->addSelect(
            DB::raw('(SELECT COUNT(`notification_history`.`id`) FROM `notification_history` WHERE `notification_history`.`channel_id` = `notification_channels`.`id` GROUP BY `notification_history`.`channel_id`) AS total_notification_history')
        );

        return $this;
    }
}
