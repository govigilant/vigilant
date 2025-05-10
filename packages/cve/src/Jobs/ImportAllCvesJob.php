<?php

namespace Vigilant\Cve\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Cve\Actions\ImportAllCves;

class ImportAllCvesJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(protected int $page = 0)
    {
        $this->onQueue(config()->string('cve.queue'));
    }

    public function handle(ImportAllCves $importer): void
    {
        $importer->import($this->page);
    }

    public function uniqueId(): int
    {
        return $this->page;
    }

    public function tags(): array
    {
        return [
            $this->page,
        ];
    }
}
