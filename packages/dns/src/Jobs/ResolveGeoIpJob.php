<?php

namespace Vigilant\Dns\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Dns\Actions\ResolveGeoIp;
use Vigilant\Dns\Models\DnsMonitor;

class ResolveGeoIpJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public DnsMonitor $monitor
    ) {
        $this->onQueue(config('dns.queue'));
    }

    public function handle(ResolveGeoIp $geoIp): void
    {
        $geoIp->resolve($this->monitor);
    }

    public function uniqueId(): int
    {
        return $this->monitor->id;
    }
}
