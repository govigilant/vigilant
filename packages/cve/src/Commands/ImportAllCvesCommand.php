<?php

namespace Vigilant\Cve\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Vigilant\Cve\Jobs\ImportCvesJob;

class ImportAllCvesCommand extends Command
{
    protected $signature = 'cve:import-all';

    protected $description = 'Import all CVEs';

    public function handle(): int
    {
        $startDate = Carbon::parse('1999-09-01');

        $index = 0;

        while ($startDate->isBefore(now())) {
            $jobs[] = (new ImportCvesJob($startDate->clone()))
                ->delay(now()->addSeconds($index * 10));

            $this->info("Added import job for {$startDate->format('Y-m-d')}");

            $startDate->addDays(30);
            $index++;
        }

        Bus::chain($jobs)
            ->onQueue(config()->string('cve.queue'))
            ->dispatch();

        return static::SUCCESS;
    }
}
