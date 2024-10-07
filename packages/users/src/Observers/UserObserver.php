<?php

namespace Vigilant\Users\Observers;

use Illuminate\Database\Eloquent\Model;
use Vigilant\Users\Jobs\SendConfirmationMailJob;

class UserObserver
{
    public function created(Model $model): void
    {
        SendConfirmationMailJob::dispatch($model);
    }
}
