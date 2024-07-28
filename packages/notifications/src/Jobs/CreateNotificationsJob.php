<?php

namespace Vigilant\Notifications\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Core\Services\TeamService;
use Vigilant\Notifications\Actions\CreateNotifications;
use Vigilant\Users\Models\Team;

class CreateNotificationsJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Team $team
    ) {
        $this->onQueue(config('notifications.queue'));
    }

    public function handle(CreateNotifications $notifications, TeamService $teamService): void
    {
        $teamService->setTeam($this->team);
        $notifications->create($this->team);
    }

    public function uniqueId(): int
    {
        return $this->team->id;
    }
}
