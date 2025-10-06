<?php

namespace Vigilant\Uptime\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Uptime\Actions\FetchGeolocation;
use Vigilant\Uptime\Models\Monitor;

class UpdateMonitorLocationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Monitor $monitor)
    {
        $this->onQueue(config('uptime.queue'));
    }

    public function handle(FetchGeolocation $fetchGeolocation): void
    {
        $target = $this->monitor->type->formatTarget($this->monitor);

        $geolocation = $fetchGeolocation->fetch($target);

        if ($geolocation === null) {
            return;
        }

        $this->monitor->updateQuietly([
            'country' => $geolocation['country'],
            'latitude' => $geolocation['latitude'],
            'longitude' => $geolocation['longitude'],
        ]);
    }
}
