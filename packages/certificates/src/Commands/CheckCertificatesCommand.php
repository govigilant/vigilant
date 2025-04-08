<?php

namespace Vigilant\Certificates\Commands;

use Illuminate\Console\Command;
use Vigilant\Certificates\Jobs\CheckCertificateJob;
use Vigilant\Certificates\Models\CertificateMonitor;

class CheckCertificatesCommand extends Command
{
    protected $signature = 'certificates:check';

    public function handle(): int
    {
        CertificateMonitor::query()
            ->whereNull('next_check')
            ->orWhere('next_check', '<=', now())
            ->get()
            ->each(fn (CertificateMonitor $monitor) => CheckCertificateJob::dispatch($monitor));

        return static::SUCCESS;
    }
}
