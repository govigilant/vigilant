<?php

namespace Vigilant\Users\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class VerifyEmail extends BaseVerifyEmail implements ShouldQueue
{
    use Queueable;
}
