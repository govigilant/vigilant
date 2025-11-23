<?php

namespace Vigilant\Notifications\Actions;

use Vigilant\Notifications\Notifications\Notification;

class CheckBurst
{
    protected const int BURST_SECONDS = 30;

    public function isBursting(Notification $notification): bool
    {
        $key = $this->cacheKey($notification);

        return ! cache()->add($key, 1, self::BURST_SECONDS);
    }

    protected function cacheKey(Notification $notification): string
    {
        return sprintf('notifications:burst:%s:%s', $notification::class, $notification->uniqueId());
    }
}
