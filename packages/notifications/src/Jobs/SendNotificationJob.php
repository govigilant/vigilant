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
use Vigilant\Notifications\Notifications\Notification;

class SendNotificationJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Notification $notification,
        public Channel $channel
    ) {
    }

    public function handle(): void
    {
        /** @var NotificationChannel $instance */
        $instance = app($this->channel->channel);

        $instance->fire($this->notification, $this->channel);
    }

    public function uniqueId(): string
    {
        return get_class($this->notification).$this->notification->uniqueId();
    }
}
