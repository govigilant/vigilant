<?php

namespace Vigilant\Notifications\Actions;

use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Models\Trigger;
use Vigilant\Notifications\Notifications\Notification;

class CheckBurst
{
    protected const int BURST_SECONDS = 30;

    public function isBursting(Notification $notification, Trigger $trigger, Channel $channel): bool
    {
        $key = $this->cacheKey($notification, $trigger, $channel);

        return ! cache()->add($key, 1, self::BURST_SECONDS);
    }

    protected function cacheKey(Notification $notification, Trigger $trigger, Channel $channel): string
    {
        return sprintf('notifications:burst:%s:%s:%s:%s', $notification::class, $notification->uniqueId(), $trigger->id, $channel->id);
    }
}
