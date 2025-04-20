<?php

namespace Vigilant\Cve\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Core\Services\TeamService;
use Vigilant\Cve\Actions\MatchExistingCves;
use Vigilant\Cve\Models\CveMonitor;

class MatchExistingCvesJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected CveMonitor $monitor
    ) {
        $this->onQueue(config()->string('cve.queue'));
    }

    public function handle(MatchExistingCves $matcher, TeamService $teamService): void
    {
        $teamService->setTeamById($this->monitor->team_id);
        $matcher->match($this->monitor);
    }
}
