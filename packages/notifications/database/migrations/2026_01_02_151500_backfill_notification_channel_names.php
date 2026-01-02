<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Vigilant\Notifications\Channels\NotificationChannel;
use Vigilant\Notifications\Models\Channel;

return new class extends Migration
{
    public function up(): void
    {
        Model::unguarded(function (): void {
            Channel::withoutEvents(function (): void {
                Channel::withoutGlobalScopes()
                    ->whereNull('name')
                    ->chunkById(100, function ($channels): void {
                        /** @var \Illuminate\Support\Collection<int, Channel> $channels */
                        $channels->each(function (Channel $channel): void {
                            $channelType = $channel->channel;

                            if (! is_string($channelType) || ! class_exists($channelType) || ! is_subclass_of($channelType, NotificationChannel::class)) {
                                return;
                            }

                            $channel->forceFill(['name' => $channelType::$name])->save();
                        });
                    });
            });
        });
    }
};
