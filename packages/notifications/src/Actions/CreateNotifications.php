<?php

namespace Vigilant\Notifications\Actions;

use Vigilant\Notifications\Facades\NotificationRegistry;
use Vigilant\Notifications\Models\Trigger;
use Vigilant\Notifications\Notifications\Notification;
use Vigilant\Users\Models\Team;

class CreateNotifications
{
    public function create(Team $team): void
    {
        $notifications = NotificationRegistry::notifications();

        /** @var class-string<Notification> $notification */
        foreach ($notifications as $notification) {
            Trigger::query()->firstOrCreate([
                'team_id' => $team->id,
                'notification' => $notification,
            ], [
                'enabled' => true,
                'name' => $notification::$name,
                'all_channels' => true,
                'conditions' => $notification::$defaultConditions,
            ]);
        }
    }
}
