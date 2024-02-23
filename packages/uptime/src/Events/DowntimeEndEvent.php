<?php

namespace Vigilant\Uptime\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Vigilant\Uptime\Models\Downtime;

class DowntimeEndEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Downtime $downtime
    ) {
    }
}
