<?php

namespace Vigilant\Lighthouse\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vigilant\Core\Services\TeamService;
use Vigilant\Lighthouse\Actions\Lighthouse;
use Vigilant\Lighthouse\Models\LighthouseMonitor;

class LighthouseJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public LighthouseMonitor $site) {
        $this->onQueue(config('lighthouse.queue'));
    }

    public function handle(Lighthouse $lighthouse, TeamService $teamService): void
    {
        $teamService->setTeamById($this->site->team_id);
        $lighthouse->run($this->site);
    }

    public function uniqueId(): int
    {
        return $this->site->id;
    }
}
