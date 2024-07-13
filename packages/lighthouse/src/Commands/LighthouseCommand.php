<?php

namespace Vigilant\Lighthouse\Commands;

use Illuminate\Console\Command;
use Vigilant\Lighthouse\Jobs\LighthouseJob;
use Vigilant\Lighthouse\Models\LighthouseMonitor;

class LighthouseCommand extends Command
{
    protected $signature = 'lighthouse {siteId}';

    protected $description = 'Run lighthouse for a site';

    public function handle(): int
    {
        /** @var int $siteId */
        $siteId = $this->argument('siteId');

        /** @var LighthouseMonitor $site */
        $site = LighthouseMonitor::query()
            ->withoutGlobalScopes()
            ->findOrFail($siteId);

        LighthouseJob::dispatch($site);

        return static::SUCCESS;
    }
}
