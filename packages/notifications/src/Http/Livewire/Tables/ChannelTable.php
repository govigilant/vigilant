<?php

namespace Vigilant\Notifications\Http\Livewire\Tables;

use Illuminate\Database\Eloquent\Model;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use Vigilant\Notifications\Channels\NotificationChannel;
use Vigilant\Notifications\Models\Channel;

class ChannelTable extends LivewireTable
{
    protected string $model = Channel::class;

    protected function columns(): array
    {
        return [
            Column::make(__('Channel'), 'channel')
                ->displayUsing(function(string $channel) {
                    /** @var class-string<NotificationChannel> $channel */

                    return $channel::$name;
                })
        ];
    }

    public function link(Model $model): ?string
    {
       return route('notifications.channel.edit', ['channel' => $model]);
    }
}
