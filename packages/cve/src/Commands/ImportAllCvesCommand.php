<?php

namespace Vigilant\Cve\Commands;

use Illuminate\Console\Command;
use Vigilant\Cve\Actions\ImportAllCves;
use Vigilant\Cve\Jobs\ImportAllCvesJob;

class ImportAllCvesCommand extends Command
{
    protected $signature = 'cve:import-all';

    protected $description = 'Import all CVEs';

    public function handle(): int
    {
        ImportAllCvesJob::dispatch();

        return static::SUCCESS;
    }
}
