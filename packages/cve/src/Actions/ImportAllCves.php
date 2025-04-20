<?php

namespace Vigilant\Cve\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Vigilant\Cve\Jobs\ImportCvesJob;

class ImportAllCves
{
    public function import(): void
    {
        $startDate = Carbon::parse('1999-09-01');

        $index = 0;
        $jobs = [];

        while ($startDate->isBefore(now())) {
            $jobs[] = (new ImportCvesJob($startDate->clone()))
                ->delay(now()->addSeconds($index * 10));

            $startDate->addDays(30);
            $index++;
        }

        Bus::chain($jobs)
            ->onQueue(config()->string('cve.queue'))
            ->dispatch();
    }
}
