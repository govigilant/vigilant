<?php

namespace Vigilant\Cve\Commands;

use Illuminate\Console\Command;
use Vigilant\Cve\Jobs\MatchCveJob;
use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Models\CveMonitor;

class MatchCveCommand extends Command
{
    protected $signature = 'cve:match {monitorId} {cveId}';

    protected $description = 'Match CVE to Monitor';

    public function handle(): int
    {
        /** @var ?int $monitorId */
        $monitorId = $this->argument('monitorId');

        /** @var ?int $cveId */
        $cveId = $this->argument('cveId');

        $monitor = CveMonitor::query()
            ->withoutGlobalScopes()
            ->findOrFail($monitorId);

        $cve = Cve::query()->findOrFail($cveId);

        MatchCveJob::dispatch($monitor, $cve);

        return static::SUCCESS;
    }
}
