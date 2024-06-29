<?php

namespace Vigilant\Uptime\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Uptime\Actions\CheckUptime;
use Vigilant\Uptime\Models\Monitor;

class CheckUptimeJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Monitor $monitor) {}

    public function handle(CheckUptime $uptime): void
    {
        $uptime->check($this->monitor);
    }

    public function uniqueId(): int
    {
        return $this->monitor->id;
    }
}
