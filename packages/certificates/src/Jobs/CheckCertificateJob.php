<?php

namespace Vigilant\Certificates\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Certificates\Actions\CheckCertificate;
use Vigilant\Certificates\Models\CertificateMonitor;

class CheckCertificateJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public CertificateMonitor $monitor
    ) {
        $this->onQueue(config()->string('certificates.queue'));
    }

    public function handle(CheckCertificate $certificate): void
    {
        $certificate->check($this->monitor);
    }

    public function uniqueId(): string
    {
        return $this->monitor->id;
    }
}
