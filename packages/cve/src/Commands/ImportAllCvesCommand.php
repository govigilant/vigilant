<?php

namespace Vigilant\Cve\Commands;

use Illuminate\Console\Command;
use Vigilant\Cve\Actions\ImportAllCves;

class ImportAllCvesCommand extends Command
{
    protected $signature = 'cve:import-all';

    protected $description = 'Import all CVEs';

    public function handle(ImportAllCves $action): int
    {
        $action->import();

        return static::SUCCESS;
    }
}
