<?php

namespace Vigilant\Certificates\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\PendingDispatch;
use Vigilant\Certificates\Jobs\CheckCertificateJob;
use Vigilant\Certificates\Models\CertificateMonitor;

class CheckCertificatesCommand extends Command
{
    protected $signature = 'certificates:check-scheduled';

    public function handle(): int
    {
        CertificateMonitor::query()
            ->whereNull('next_check')
            ->orWhere('next_check', '<=', now())
            ->get()
            ->each(fn (CertificateMonitor $monitor): PendingDispatch => CheckCertificateJob::dispatch($monitor));

        return static::SUCCESS;
    }
}
