<?php

namespace Vigilant\Cve\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Cve\Actions\ImportCveYear;

class ImportCveYearJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(protected int $year)
    {
        $this->onQueue(config()->string('cve.queue'));
    }

    public function handle(ImportCveYear $importer): void
    {
        $importer->import($this->year);
    }

    public function uniqueId(): int
    {
        return $this->year;
    }

    public function tags(): array
    {
        return [
            $this->year,
        ];
    }
}
