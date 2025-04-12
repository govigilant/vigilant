<?php

namespace Vigilant\Certificates\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\PendingDispatch;
use Vigilant\Certificates\Jobs\CheckCertificateJob;
use Vigilant\Certificates\Models\CertificateMonitor;

class CheckCertificatesCommand extends Command
{
    protected $signature = 'certificates:check-scheduled';

    public function handle(): int
    {
        CertificateMonitor::query()
            ->withoutGlobalScopes()
            ->where('enabled', '=', true)
            ->where(function (Builder $query): void {
                $query->where('next_check', '<=', now())
                    ->orWhereNull('next_check');
            })
            ->get()
            ->each(fn (CertificateMonitor $monitor): PendingDispatch => CheckCertificateJob::dispatch($monitor));

        return static::SUCCESS;
    }
}
