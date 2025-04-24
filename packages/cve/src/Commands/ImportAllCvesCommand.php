<?php

namespace Vigilant\Cve\Commands;

use Illuminate\Console\Command;
use Vigilant\Cve\Jobs\ImportAllCvesJob;

class ImportAllCvesCommand extends Command
{
    protected $signature = 'cve:import-all {page=0}';

    protected $description = 'Import all CVEs';

    public function handle(): int
    {
        /** @var int $page */
        $page = $this->argument('page');

        ImportAllCvesJob::dispatch($page);

        return static::SUCCESS;
    }
}
