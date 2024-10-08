<?php

namespace Vigilant\Uptime\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Vigilant\Uptime\Models\Monitor;

class UptimeCheckedEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Monitor $monitor
    ) {}
}
