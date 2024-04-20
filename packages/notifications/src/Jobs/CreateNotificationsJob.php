<?php

namespace Vigilant\Notifications\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
    }

    public function handle(CreateNotifications $notifications): void
    {
        $notifications->create($this->team);
    }

    public function uniqueId(): int
    {
        return $this->team->id;
    }
}
