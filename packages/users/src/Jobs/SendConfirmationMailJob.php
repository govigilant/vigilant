<?php

namespace Vigilant\Users\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Users\Models\User;

class SendConfirmationMailJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public User $user)
    {
        $this->onQueue(config('users.queue'));
    }

    public function handle(): void
    {
        $this->user->sendEmailVerificationNotification();
    }

    public function uniqueId(): int
    {
        return $this->user->id;
    }
}
