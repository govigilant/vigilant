<?php

namespace Vigilant\Cve\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Models\CveMonitor;

class MatchCveMonitorsJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected Cve $cve
    ) {
        $this->onQueue(config()->string('cve.queue'));
    }

    public function handle(): void
    {
        CveMonitor::query()
            ->withoutGlobalScopes()
            ->each(function (CveMonitor $monitor): void {
                MatchCveJob::dispatch($monitor, $this->cve);
            });
    }

    public function uniqueId(): int
    {
        return $this->cve->id;
    }
}
