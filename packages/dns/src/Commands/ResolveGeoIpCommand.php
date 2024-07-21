<?php

namespace Vigilant\Dns\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Vigilant\Dns\Enums\Type;
use Vigilant\Dns\Jobs\ResolveGeoIpJob;
use Vigilant\Dns\Models\DnsMonitor;

class ResolveGeoIpCommand extends Command
{
    protected $signature = 'dns:geoip {monitorId?}';

    protected $description = 'Resolve GeoIP for monitor';

    public function handle(): int
    {
        /** @var ?int $monitorId */
        $monitorId = $this->argument('monitorId');

        DnsMonitor::query()
            ->withoutGlobalScopes()
            ->when($monitorId !== null, fn (Builder $query) => $query->where('id', '=', $monitorId))
            ->whereIn('type', Type::geoIpableTypes())
            ->lazy()
            ->each(fn (DnsMonitor $monitor) => ResolveGeoIpJob::dispatch($monitor));

        return static::SUCCESS;
    }
}
