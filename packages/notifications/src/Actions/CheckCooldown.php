<?php

namespace Vigilant\Notifications\Actions;

use Vigilant\Notifications\Models\Channel;
use Vigilant\Notifications\Models\History;
use Vigilant\Notifications\Models\Trigger;
use Vigilant\Notifications\Notifications\Notification;

class CheckCooldown
{
    public function onCooldown(Trigger $trigger, Channel $channel): bool
    {
        $cooldown = $trigger->cooldown;

        if ($cooldown === null) {
            /** @var class-string<Notification> $notification */
            $notification = $trigger->notification;

            $cooldown = $notification::$defaultCooldown;
        }

        if ($cooldown === null || $cooldown === 0) {
            return false;
        }

        /** @var ?History $lastNotification */
        $lastNotification = $channel->history()
            ->where('trigger_id', '=', $trigger->id)
            ->where('uniqueId', '=', $notification->uniqueId())
            ->orderByDesc('created_at')
            ->first();

        if ($lastNotification === null) {
            return false;
        }

        $minutesSinceLastNotification = $lastNotification->created_at?->diffInMinutes() ?? 0;

        return $minutesSinceLastNotification < $cooldown;
    }
}
