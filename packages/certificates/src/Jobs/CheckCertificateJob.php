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
use Vigilant\Core\Services\TeamService;

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

    public function handle(CheckCertificate $certificate, TeamService $teamService): void
    {
        $teamService->setTeamById($this->monitor->team_id);
        $certificate->check($this->monitor);
    }

    public function uniqueId(): int
    {
        return $this->monitor->id;
    }
}
