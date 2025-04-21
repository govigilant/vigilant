<?php

namespace Vigilant\Cve\Actions;

use Illuminate\Support\Facades\Bus;
use Vigilant\Cve\Jobs\ImportCveYearJob;

class ImportAllCves
{
    public function import(): void
    {
        $year = 2002;

        $index = 0;
        $jobs = [];

        while ($year <= now()->year) {
            $jobs[] = (new ImportCveYearJob($year))
                ->delay(now()->addSeconds($index * 10));

            $year++;
            $index++;
        }

        Bus::chain($jobs)
            ->onQueue(config()->string('cve.queue'))
            ->dispatch();
    }
}
