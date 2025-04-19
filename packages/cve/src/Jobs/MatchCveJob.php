<?php

namespace Vigilant\Cve\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Core\Services\TeamService;
use Vigilant\Cve\Actions\MatchCve;
use Vigilant\Cve\Models\Cve;
use Vigilant\Cve\Models\CveMonitor;

class MatchCveJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected CveMonitor $monitor,
        protected Cve $cve
    ) {
        $this->onQueue(config()->string('cve.queue'));
    }

    public function handle(MatchCve $matcher, TeamService $teamService): void
    {
        $teamService->setTeamById($this->monitor->team_id);
        $matcher->match($this->monitor, $this->cve);
    }
}
