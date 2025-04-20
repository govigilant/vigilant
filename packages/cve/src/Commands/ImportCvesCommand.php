<?php

namespace Vigilant\Cve\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Vigilant\Cve\Jobs\ImportCvesJob;

class ImportCvesCommand extends Command
{
    protected $signature = 'cve:import {from?}';

    protected $description = 'Import new CVEs';

    public function handle(): int
    {
        /** @var ?string $from */
        $from = $this->argument('from');

        $from = $from !== null ? Carbon::parse($from) : null;

        ImportCvesJob::dispatch($from);

        return static::SUCCESS;
    }
}
