<?php

namespace Vigilant\Dns\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\PendingDispatch;
use Vigilant\Dns\Jobs\CheckDnsRecordJob;
use Vigilant\Dns\Models\DnsMonitor;

class CheckAllDnsRecordsCommand extends Command
{
    protected $signature = 'dns:check-all';

    protected $description = 'Check All DNS Monitors';

    public function handle(): int
    {
        DnsMonitor::query()
            ->get()
            ->each(fn (DnsMonitor $monitor): PendingDispatch => CheckDnsRecordJob::dispatch($monitor));

        return static::SUCCESS;
    }
}
