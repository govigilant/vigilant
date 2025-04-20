<?php

namespace Vigilant\Cve\Commands;

use Illuminate\Console\Command;
use Vigilant\Cve\Jobs\MatchExistingCvesJob;
use Vigilant\Cve\Models\CveMonitor;

class MatchExistingCvesCommand extends Command
{
    protected $signature = 'cve:match-existing {monitorId?}';

    protected $description = 'Match existing CVEs';

    public function handle(): int
    {
        /** @var ?int $monitorId */
        $monitorId = $this->argument('monitorId');

        $monitors = CveMonitor::query()
            ->withoutGlobalScopes()
            ->when($monitorId, function ($query) use ($monitorId) {
                return $query->where('id', $monitorId);
            })
            ->get();

        foreach ($monitors as $monitor) {
            MatchExistingCvesJob::dispatch($monitor);
        }

        return static::SUCCESS;
    }
}
