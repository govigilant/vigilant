<?php

namespace Vigilant\Notifications\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Notifications\Channels\NotificationChannel;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Models\Trigger;
use Vigilant\Notifications\Notifications\Notification;

class SendNotificationJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Notification $notification,
        public Channel $channel,
        public ?Trigger $trigger = null,
    ) {
    }

    public function handle(): void
    {
        /** @var NotificationChannel $instance */
        $instance = app($this->channel->channel);

        $instance->fire($this->notification, $this->channel);

        $this->channel->history()->create([
            'trigger_id' => $this->trigger?->id ?? null,
            'notification' => get_class($this->notification),
            'uniqueId' => $this->notification->uniqueId(),
            'data' => $this->notification->toArray(),
        ]);
    }

    public function uniqueId(): string
    {
        return implode('-', [
            get_class($this->notification),
            $this->channel->id,
            $this->trigger?->id ?? 0,
            $this->notification->uniqueId()
        ]);
    }
}
