<?php

namespace Vigilant\Notifications\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Vigilant\Core\Services\TeamService;
use Vigilant\Notifications\Channels\NotificationChannel;
use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Notifications\Notification;

class SendNotificationJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public Notification $notification,
        public int $teamId,
        public int $channelId,
        public ?int $triggerId = null,
    ) {
    }

    public function handle(TeamService $teamService): void
    {
        $teamService->setTeamById($this->teamId);

        /** @var Channel $channel */
        $channel = Channel::query()->findOrFail($this->channelId);

        /** @var NotificationChannel $instance */
        $instance = app($channel->channel);

        $instance->fire($this->notification, $channel);

        $channel->history()->create([
            'trigger_id' => $this->triggerId,
            'notification' => get_class($this->notification),
            'uniqueId' => $this->notification->uniqueId(),
            'data' => $this->notification->toArray(),
        ]);
    }

    public function uniqueId(): string
    {
        return implode('-', [
            get_class($this->notification),
            $this->channelId,
            $this->triggerId ?? 0,
            $this->notification->uniqueId(),
        ]);
    }
}
