<?php

namespace Vigilant\Users\Observers;

use Illuminate\Database\Eloquent\Model;
use Vigilant\Users\Jobs\SendConfirmationMailJob;
use Vigilant\Users\Models\User;

class UserObserver
{
    /** @param User $model */
    public function created(Model $model): void
    {
        SendConfirmationMailJob::dispatch($model);
    }
}
