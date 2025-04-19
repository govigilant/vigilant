<?php

namespace Vigilant\Cve\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Vigilant\Cve\Actions\ImportCves;

class ImportCvesJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 3;

    public $backoff = 30;

    public function __construct(protected ?Carbon $from = null)
    {
        $this->onQueue(config()->string('cve.queue'));
    }

    public function handle(ImportCves $importer): void
    {
        $importer->import($this->from);
    }

    public function uniqueId(): string
    {
        return $this->from?->format('Y-m-d') ?? 'recent';
    }

    public function tags(): array
    {
        return [
            $this->from?->format('Y-m-d') ?? 'recent',
        ];
    }
}
