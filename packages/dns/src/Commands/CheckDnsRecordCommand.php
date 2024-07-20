<?php

namespace Vigilant\Dns\Commands;

use Illuminate\Console\Command;
use Vigilant\Dns\Jobs\CheckDnsRecordJob;
use Vigilant\Dns\Models\DnsMonitor;

class CheckDnsRecordCommand extends Command
{
    protected $signature = 'dns:check {recordId}';

    protected $description = 'Check DNS Monitor';

    public function handle(): int
    {
        /** @var int $recordId */
        $recordId = $this->argument('recordId');

        /** @var DnsMonitor $monitor */
        $monitor = DnsMonitor::query()
            ->withoutGlobalScopes()
            ->findOrFail($recordId);

        CheckDnsRecordJob::dispatch($monitor);

        return static::SUCCESS;
    }

}
