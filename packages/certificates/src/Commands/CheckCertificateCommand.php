<?php

namespace Vigilant\Certificates\Commands;

use Illuminate\Console\Command;
use Vigilant\Certificates\Jobs\CheckCertificateJob;
use Vigilant\Certificates\Models\CertificateMonitor;

class CheckCertificateCommand extends Command
{
    protected $signature = 'certificates:check {id}';

    public function handle(): int
    {
        /** @var int $id */
        $id = $this->argument('id');

        $monitor = CertificateMonitor::query()
            ->withoutGlobalScopes()
            ->findOrFail($id);

        CheckCertificateJob::dispatch($monitor);

        return static::SUCCESS;
    }
}
